<template>
<div>
    <teacher-list :source_url="source"   :search_params="listSettings.params"
        :version="version"  :title_text="listSettings.title_text"
        :no_search="listSettings.no_search" :can_remove="listSettings.can_remove"
        :no_page="listSettings.no_page"   :can_select="listSettings.can_select"
        :create_text="listSettings.create_text" 
        @details="onDetails"
        @begin-create="beginEdit" @remove-clicked="beginDelete">
    
    </teacher-list>
   
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeleteCanceled" @confirmed="removeTeacher">        
    </delete-confirm>

    <modal effect="fade" :showbtn="false"  :show.sync="editSettings.show"  
          :width="editSettings.width"@closed="endEdit" >
        
        <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="endEdit">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           
        </div>
        <div slot="modal-body" class="modal-body">
            <teacher-selector v-if="editSettings.show" 
            :source_url="selectorSettings.source" :details_link="selectorSettings.details_link"
            :params="selectorSettings.params" :title_text="selectorSettings.title"
            @submit="onTeacherSelected"  >
            </teacher-selector>
        </div>
    </modal> 
    

</div>
</template>
<script>
    
    import TeacherList from '../../../components/teacher/list.vue'
    import TeacherSelector from '../../../components/teacher/selector.vue'
    export default {
        name:'GroupTeacherView',
        components: {
            'teacher-selector':TeacherSelector,
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
                source:GroupTeacher.source(),
                version:0,
                can_edit:false,

                listSettings:{
                    no_page:true,
                    title_text:'群組教師',
                    create_text:'加入教師',
                    no_search:true,
                    can_select:false,
                    can_remove:true,
                    params:{
                        parent : this.teacher.user_id
                    }
                },

                selectorSettings:{
                    title:'請選擇要加入此群組的教師',
                    details_link:false,
                    params:{
                        center:0,
                        parent:this.teacher.user_id
                    },
                    source:GroupTeacher.createUrl(),
                },

                teacherId:0,
                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                },
                editSettings:{
                    source:GroupTeacher.createUrl(),
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
            refresh(){
                this.version +=1
            },
            onDataLoaded(admin){
                this.$emit('data-loaded',admin)
            },     
            onDetails(id){
                let path='/teachers/' + id
                Helper.newWindow(path)
            },   
            beginEdit() {
                this.editSettings.show=true               
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
            onTeacherSelected(teacher_ids){
               this.addTeachers(teacher_ids)
            },
            addTeachers(teacher_ids){
                let form=new Form({
                    group_id:this.teacherId,
                    teacher_ids:teacher_ids
                })
                let save=GroupTeacher.store(form)
                save.then(result => {
                    Helper.BusEmitOK('新增成功')
                    this.refresh()
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
