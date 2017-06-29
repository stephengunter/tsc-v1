<template>
<div>
   
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  :can_back="can_back"  
       :version="version"  @begin-edit="beginEdit" @user-loaded="onUserLoaded"
       @btn-back-clicked="onBtnBackClicked"   @btn-delete-clicked="beginDelete"                
       @add-role="onAddRole" >
    </show>

    <edit v-else :id="id" :role="role"
       @saved="onUserSaved"   @canceled="onEditCanceled" >                 
    </edit>
    
    
    <user-roles :user_id="id" :showing="userRoles.show"
     @canceled="onUserRolesCanceled" >
        
    </user-roles>
    
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteUser">        
    </delete-confirm>
</div>
</template>
<script>
    import Show from '../../components/user/show.vue'
    import Edit from '../../components/user/edit.vue'

    import UserRoles from '../../components/user/user-roles.vue'
    

    export default {
        name:'User',
        components: {
            Show,
            Edit,
            'user-roles':UserRoles
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            role:{
               type: String,
               default: 'User'
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        
        
        data() {
            return {
                
                readOnly:true,

                userRoles:{
                    show:false,
                    adding:true,
                },

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

               this.userRoles={
                    show:false,
                    adding:true,
                }

              
            },
            onUserLoaded(user){
                this.$emit('user-loaded',user)
            }, 
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onUserSaved(user){
                this.init()
                this.$emit('saved',user)
            },
            
            onAddRole(){
                this.userRoles.show=true
            },
            onUserRolesCanceled() {
               this.userRoles.show = false
            },           
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
           
            beginDelete(values){
                this.deleteConfirm.msg='確定要刪除使用者 ' + values.name + ' 的資料嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                
            },
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteUser(){
                let id = this.deleteConfirm.id 
                let remove= User.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('user-deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },

            
        }
    }
</script>
