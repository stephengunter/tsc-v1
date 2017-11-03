<template>
<div>
    
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  :can_back="can_back"  
       :version="version"  @begin-edit="beginEdit" @loaded="onDataLoaded"
       @btn-back-clicked="onBtnBackClicked"   @btn-delete-clicked="beginDelete" 
       @edit-review="onEditReview">                 
    </show>

    <edit v-else :id="id" 
       @saved="onSaved"   @canceled="onEditCanceled" >                 
    </edit> 

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteCourse">        
    </delete-confirm>
    
    <review-editor :showing="showReviewEditor" :reviewed="courseReviewed"
      @close="showReviewEditor=false" @save="updateReview">
    </review-editor>

</div>
</template>
<script>
    import Show from '../../components/course/show.vue'
    import Edit from '../../components/course/edit.vue'


    export default {
        name:'Course',
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
                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                },
               
                showReviewEditor:false,
                courseReviewed:false,    
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
               this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
               }
               this.showReviewEditor=false
               this.courseReviewed=false
            },      
            onDataLoaded(course){
                this.courseReviewed=Helper.isTrue(course.reviewed)
                this.$emit('loaded',course)
            },        
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onSaved(course){
                this.init()
                this.$emit('saved',course)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            beginDelete(values){
                this.deleteConfirm.msg='確定要刪除 ' + values.name + ' 的課程資料嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                
            },
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteCourse(){
                let id = this.deleteConfirm.id 
                let remove= Course.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('deleted')
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
                let id = this.id 
                let review= val
                let save= CourseReview.update(id,review)

                save.then(course => {
                    Helper.BusEmitOK('存檔成功')
                    this.init()
                   
                    this.$emit('review-updated',course)
                })
                .catch(error => {
                    Helper.BusEmitError(error,'存檔失敗')
                    this.showReviewEditor=false   
                })
            }
            
        }
    }
</script>
