<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">           
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>           
        </div> <!--  panel  heading -->
        <div class="panel-body">
            <user-checker v-if="newUser"
               :version="userCheckerSettings.version"
               :status="userCheckerSettings.status" :col_width="userCheckerSettings.col_width"
               @checked="onUserChecked"  @check-failed="onUserCheckFailed" >
                  
            </user-checker>
            <form v-show="user_checked" v-if="loaded" @keydown="clearErrorMsg($event.target.name)" @submit.prevent="onSubmit" class="form">
                <teacher-inputs :form="form" 
                   :with_user="inputsSettings.with_user" :with_profile="inputsSettings.with_profile"
                   :with_teacher_name="inputsSettings.with_teacher_name"
                   @dob-selected="setDOB" 
                  @active-selected="setActive"   @reviewed-selected="setReviewed"
                  @gender-selected="setGender" @canceled="onCanceled" >
                </teacher-inputs>

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
    import TeacherInputs from '../../components/teacher/inputs.vue'
    import UserChecker from '../../components/user/checker.vue'
    import UserSelector from '../../components/user/selector.vue'
    export default {
        name: 'TeacherCreate',
        components: {
            'teacher-inputs':TeacherInputs,
            'user-checker':UserChecker,
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
                title:Helper.getIcon('Teachers')  + '  新增教師',
                
                loaded:false,

                newUser:true,

                user_checked: false,
                userCheckerSettings:{
                    version:0,
                    status:0,
                    col_width:4
                },
                inputsSettings:{
                    with_teacher_name:false,
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
                    teacher:{}
                }),

                userList:[],
                showUserList:false,
                modalSettings:{
                   width:1200,
                },
             
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
                    teacher:{
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

                let create=Teacher.create(this.user_id)
                create.then(data=>{
                     let user=data.user
                     let teacher=data.teacher

                     this.setUser(user)
                     this.form.teacher=teacher
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
            setDOB(data){
                this.form.user.profile.dob=data
              
                this.clearErrorMsg('user.profile.dob')
            },
            setActive(val){
                this.form.teacher.active=val
            },
            setReviewed(val){
                this.form.teacher.reviewed=val
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
                if(name=='user.profile.fullname'){
                    this.form.errors.clear(user.name)
                }
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
                let experience=Helper.replaceAll( this.form.teacher.experiences, '\n','<br>')
                this.form.teacher.experiences=experience
                
                let store=Teacher.store(this.form)
                    .then(data => {
                       Helper.BusEmitOK()
                       this.$emit('saved',data)                            
                    })
                    .catch(error => {
                        if(error.status==422){
                           // this.dobError=this.form.errors.has('user.profile.dob') 
                        }else{
                            Helper.BusEmitError(error,'存檔失敗') 
                        }
                       
                    })
            },
           
            
            
        },

    }
</script>