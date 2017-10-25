<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">           
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span> 
            <label  class="btn  btn-success btn-file" @click="resetImport">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                Excel 匯入
                <input type="file"  ref="fileinput"  name="teachers_file" style="display: none;"  
                @change="onFileChange" >
            </label>          
        </div> <!--  panel  heading -->
        <div class="panel-body">
            <user-checker v-if="newUser"
               :version="userCheckerSettings.version"
               :status="userCheckerSettings.status" :col_width="userCheckerSettings.col_width"
               @checked="onUserChecked"  @check-failed="onUserCheckFailed" >
                  
            </user-checker>
            <form v-show="user_checked" v-if="loaded" @keydown="clearErrorMsg($event.target.name)" @submit.prevent="onSubmit" class="form">
                <user-inputs :form="form" :with_user="inputsSettings.with_user"
                   :with_profile="inputsSettings.with_profile"
                   @gender-selected="setGender"  @dob-selected="setDOB"   >
                </user-inputs>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>角色</label>
                            <div>
                                <toggle :items="roleOptions"   :default_val="form.admin.role" @selected=setRole></toggle>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label>所屬中心</label>
                        <select v-model="form.admin.center_id"  name="admin.center_id" class="form-control" >
                            <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        
                    </div>
                </div>   <!-- row    -->
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">                           
                           <button type="submit"  class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                           <button type="button" class="btn btn-default" @click.prevent="onCanceled">取消</button>  
                        </div>
                    </div>
      
                </div><!-- row    -->
            </form>
           
        </div> <!--  panel  body -->
    </div>  <!--  panel  -->

    <modal :showbtn="false"  :show="showUserList" effect="fade" :width="modalSettings.width">
        
          <div slot="modal-header" class="modal-header modal-header-danger">
             <button id="close-button" type="button" class="close" data-dismiss="modal" @click="showUserList=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
             </button>
             <h3>
                 <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                 相同資料的使用者已經存在
             </h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <user-selector :users=userList @selected="onUserSelected"></user-selector>
        </div>
    </modal>


</div>   
</template>

<script>
    import UserChecker from '../../components/user/checker.vue'
    import UserInputs from '../../components/user/inputs.vue'
    import UserSelector from '../../components/user/selector.vue'
    export default {
        name: 'AdminCreate',
        components: {
            'user-checker':UserChecker,
            'user-inputs':UserInputs,
            'user-selector':UserSelector
        },
        props: {
            user_id:{
               type: Number,
               default: 0
            },
        },   
        data() {
            return {
                title:Helper.getIcon('Admins')  + '  新增系統管理員',
                
                loaded:false,

                newUser:true,

                user_checked: false,
                userCheckerSettings:{
                    version:0,
                    status:0,
                    col_width:4
                },
                inputsSettings:{
                    with_user:false,
                    with_profile:true,
                },
                form : new Form({
                    user:{
                       email:'',
                       phone:'',
                       profile:{
                          fullname:'',
                       }
                    },
                    admin:{}
                }),
                
               
                roleOptions:[],
                centerOptions:[],

                userList:[],
                showUserList:false,
                modalSettings:{
                   width:1200,
                },

                files: [],
             
            }
        },
        computed: {
            creating() {
               if(this.selectedSignups.length < 1) return false
               return !this.submitting 
            }
        }, 
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                this.form = new Form({
                    user:{
                       email:'',
                       phone:'',
                       profile:{
                          fullname:'',
                       }
                    },
                    admin:{
                    }
                })

                this.formSubmitting=false
                this.user_checked= false
                this.loaded=false

                this.dob={
                    time: ''
                }
                this.dobError=false

                this.userList=[]
                this.showUserList=false

                let create=Admin.create(this.user_id)
                create.then(data=>{

                     let user=data.user
                     let admin=data.admin

                     this.setUser(user)
                     this.form.admin=admin

                     this.roleOptions=data.roleOptions
                     this.centerOptions=data.centerOptions
                     this.loaded=true

                }).catch(error =>{
                     Helper.BusEmitError(error)
                     this.loaded=false
                })
             
            },
            setUser(user){
               if(user.id){
                   this.newUser=false
                   this.user_checked=true

                   this.inputsSettings.with_user=true
                   this.inputsSettings.with_profile=true
                  
               }else{
                   this.newUser=true
                   this.user_checked=false

                   this.inputsSettings.with_user=false
                   this.inputsSettings.with_profile=true
              } 

               this.form.user=user
                    
            },
            onUserChecked(user){
                this.user_checked=true
                this.form.user.phone=user.phone
                this.form.user.email=user.email
                this.form.user.profile.fullname=user.profile.fullname

                if(!this.form.user.id){
                   this.form.user.name=user.profile.fullname
                }

                if(this.formSubmitting) {
                   this.submitForm()
                }

            },
            onUserCheckFailed(userList){
                this.formSubmitting=false
                this.userList=userList
                if(userList.length){
                   this.showUserList=true
                }
            },           
            onUserSelected(selected){
                let id=selected[0]
                let url=User.showUrl(id)
                Helper.redirect(url)
            },
            setGender(val) {
                this.form.user.profile.gender = val;
            },
            setDOB(val){
                this.form.user.profile.dob=val
              
                this.clearErrorMsg('user.profile.dob')
            },
          
            setRole(val){
                this.form.admin.role=val
            },
            clearErrorMsg(name) {
                this.form.errors.clear()
                
            },
            onSubmit() {
                if(!this.form.user.id){
                   this.form.user.name=this.form.user.profile.fullname
                   this.formSubmitting=true
                   this.userCheckerSettings.status+=1
                }else{
                    this.submitForm()
                }
                
            },
            onCanceled(){
                this.$emit('canceled')
            },
            submitForm() {

                let store=Admin.store(this.form)
                    .then(data => {
                       Helper.BusEmitOK()
                       this.$emit('saved',data)                            
                    })
                    .catch(error => {
                        Helper.BusEmitError(error,'存檔失敗') 
                    })
            },
            resetImport(){
               this.$refs.fileinput.value = null
            },
            onFileChange(e) {
              
                var files = e.target.files || e.dataTransfer.files
                if (!files.length)  return
                   
                this.files = e.target.files

                this.submitImport()
            },
            submitImport() {
                

                let form = new FormData()
                for (let i = 0; i < this.files.length; i++) {
                    form.append('admins_file', this.files[i])
                    
                }

                let store=Admin.import(form)
                store.then(result => {
                        Helper.BusEmitOK()
                        this.$emit('imported')  
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            },
           
            
            
        },

    }
</script>