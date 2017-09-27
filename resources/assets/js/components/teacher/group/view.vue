<template>
<div>
    
    <list v-if="readOnly"  :group_id="teacherId" :hide_create="!can_edit"  
         @begin-edit="beginEdit" 
       @btn-back-clicked="onBtnBackClicked"   @btn-delete-clicked="beginDelete" >                 
    </list>

   <!--  <edit v-else :id="id" 
       @saved="onSaved"   @canceled="onEditCanceled" >                 
    </edit> 

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeleteCanceled" @confirmed="deleteAdmin">        
    </delete-confirm> -->
    

</div>
</template>
<script>
    import List from './list.vue'
    //import Edit from './edit.vue'



    export default {
        name:'GroupTeacherView',
        components: {
            List,
            //Edit,
        },
        props: {
            teacher: {
              type: Object,
              default: null
            },
            
        },
        data() {
            return {
                readOnly:true,
                can_edit:false,
                teacherId:0,
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
        
        methods: {
            init() {
               this.readOnly=true
               this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
               },
               this.teacherId=this.teacher.user_id
               this.can_edit=this.teacher.canEdit
            },      
            onDataLoaded(admin){
                this.$emit('data-loaded',admin)
            },        
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onSaved(admin){
                this.init()
                this.$emit('saved',admin)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onDeleteCanceled(){
                this.deleteConfirm.show=false
            },
            beginDelete(values){
                this.deleteConfirm.msg='確定要刪除 ' + values.name + ' 的系統管理員資料嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                 
            },
            deleteAdmin(){
                let id = this.deleteConfirm.id 
                let remove= Admin.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.init()
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
