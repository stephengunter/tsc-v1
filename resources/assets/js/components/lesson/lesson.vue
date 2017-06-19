<template>
<div>
    
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  :can_back="can_back"  
       :version="version"  @begin-edit="beginEdit" @loaded="onDataLoaded"
       @btn-back-clicked="onBtnBackClicked"   @btn-delete-clicked="beginDelete" >                 
    </show>

    <edit v-else :id="id" 
       @saved="onSaved"   @canceled="onEditCanceled" >                 
    </edit> 

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteLesson">        
    </delete-confirm>
    

</div>
</template>
<script>
    import Show from '../../components/lesson/show.vue'
    import Edit from '../../components/lesson/edit.vue'


    export default {
        name:'Lesson',
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

                }    
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
            },      
            onDataLoaded(lesson){
                this.$emit('loaded',lesson)
            },        
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onSaved(lesson){
                this.init()
                this.$emit('saved',lesson)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            beginDelete(values){
                this.deleteConfirm.msg= values.name
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                
            },
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteLesson(){
                let id = this.deleteConfirm.id 
                let remove= Lesson.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.deleteConfirm.show=false
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },
            
        }
    }
</script>
