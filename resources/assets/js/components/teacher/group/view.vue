<template>
<div>
    
    <list   :group_id="teacherId" :can_edit="can_edit"  
       :version="version"
         @begin-edit="beginEdit"  @begin-delete="beginDelete" >                 
     
    </list>

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeleteCanceled" @confirmed="removeTeacher">        
    </delete-confirm>

     <modal :showbtn="false" :width="editSettings.width" :show.sync="editSettings.show"  @closed="endEdit" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="endEdit">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           
          </div>
        <div slot="modal-body" class="modal-body">
           
            <teacher-list v-if="editSettings.show" :group="teacherId"
               :hide_create="editSettings.hide_create" 
                @selected="onTeacherSelected">
            </teacher-list>
 
      
        </div>
    </modal> 
    

</div>
</template>
<script>
    import List from './list.vue'
    import TeacherList from '../../../components/teacher/list.vue'

    export default {
        name:'GroupTeacherView',
        components: {
            List,
            'teacher-list':TeacherList
        },
        props: {
            teacher: {
              type: Object,
              default: null
            },
            
        },
        data() {
            return {
              
                version:0,
                can_edit:false,
                teacherId:0,
                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                },
                editSettings:{
                    width:1200,
                    show:false,
                    hide_create:true
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
               this.can_edit= Helper.isTrue(this.teacher.canEdit)
            },      
            onDataLoaded(admin){
                this.$emit('data-loaded',admin)
            },        
            beginEdit() {
                this.editSettings.show=true
               // this.readOnly=false
            },
            endEdit(){
                this.editSettings.show=false
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
            beginDelete(values){
                this.deleteConfirm.msg='確定要將 ' + values.name + ' 從群組中移除嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                 
            },  
            onTeacherSelected(id,isLink){
                this.addTeacher(id)
            },
            addTeacher(add_teacher_id){
                let form=new Form({
                    group_id:this.teacherId,
                    teacher_id:add_teacher_id
                })
                let save=GroupTeacher.store(form)
                save.then(result => {
                    Helper.BusEmitOK('新增成功')
                    this.version += 1
                    this.endEdit()
                })
                .catch(error => {
                    Helper.BusEmitError(error,'新增失敗')
                    this.endEdit() 
                })
            },
            removeTeacher(){
                let id = this.deleteConfirm.id 
                let remove=GroupTeacher.delete(this.teacherId,id)
                remove.then(result => {
                    Helper.BusEmitOK('移除成功')
                    this.version += 1
                    this.deleteConfirm.show=false
                })
                .catch(error => {
                    Helper.BusEmitError(error,'移除失敗')
                    this.deleteConfirm.show=false   
                })
            },
            onDeleteCanceled(){
                this.deleteConfirm.show=false
            },
            
           
            
            
            
        }
    }
</script>
