<template>
<div>
    
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  :can_back="can_back"  
       :version="version"  @begin-edit="beginEdit" @dataLoaded="onDataLoaded"
       @btn-back-clicked="onBtnBackClicked"   @btn-delete-clicked="beginDelete" 
       @edit-review="onEditReview">                 
    </show>

    <edit v-else :id="id" 
       @saved="onTeacherSaved"   @canceled="onEditCanceled" >                 
    </edit> 

    <delete-confirm :showing="showConfirm" :message="confirmMsg"
      @close="closeConfirm" @confirmed="deleteTeacher">        
    </delete-confirm>

    <review-editor :showing="showReviewEditor" :reviewed="teacherReviewed"
      @close="showReviewEditor=false" @save="updateReview">
    </review-editor>
    

</div>
</template>
<script>
    import Show from '../../components/teacher/show.vue'
    import Edit from '../../components/teacher/edit.vue'


    export default {
        name:'Teacher',
        components: {
            Show,
            Edit,
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },          
            can_back:{
              type: Boolean,
              default: true
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                readOnly:true,
                confirmMsg:'',
                deleteId:0,
                showConfirm:false,    

                teacher_id:0,

                
                showReviewEditor:false,
                teacherReviewed:false,
                
            }
        },
        beforeMount(){
            this.init()
        },
        watch: {
            'id': 'init',
            'version':'init'
        },
        methods: {
            init() {
               this.readOnly=true
               this.teacher_id=0
               this.confirmMsg=''
               this.deleteId=0
               this.showConfirm=false

               this.showReviewEditor=false
               this.teacherReviewed=false
            },      
            onDataLoaded(teacher){
                this.teacher_id=teacher.user_id
                this.teacherReviewed=Helper.isTrue(teacher.reviewed)
                this.$emit('data-loaded',teacher)
            },        
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onTeacherSaved(teacher){
                this.init()
                this.$emit('saved',teacher)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            beginDelete(values){
                this.confirmMsg='確定要刪除 ' + values.name + ' 的教師資料嗎？'
                this.deleteId=values.id
                this.showConfirm=true                
            },
            closeConfirm(){
                this.showConfirm=false
            },
            deleteTeacher(){
                let id = this.deleteId 
                let remove= Teacher.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('teacher-deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },
            onEditReview(){
                this.showReviewEditor=true     
            },
            updateReview(val){
                let id = this.teacher_id 
                let review= val
                let save= TeacherReview.update(id,review)

                save.then(teacher => {
                    Helper.BusEmitOK('存檔成功')
                    this.init()
                   
                    this.$emit('review-updated')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'存檔失敗')
                    this.showReviewEditor=false   
                })
            }
            
        }
    }
</script>
