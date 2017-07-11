<template>
<div>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
            <span class="panel-title">
                <h4 v-html="title"></h4> 
            </span>           
        </div>
        <div class="panel-body">
            <form  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form">
                <user-checker :version="userCheckerSettings.version"
                   :status="userCheckerSettings.status" 
                   @checked="onUserChecked"  @check-failed="onUserCheckFailed" >
                  
                </user-checker>
                <div class="row" v-if="user_checked">
                   <div  class="col-sm-3"> 
                       <div class="form-group">                          
                          <label>性別</label>
                           <div>
                              <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected=setGender></toggle>
                            </div>
                       </div>
                   </div>  
                   <div  class="col-sm-3"> 
                       <div class="form-group">                          
                          <label>身分證號</label>
                          <input type="text" name="user.profile.SID" class="form-control" v-model="form.user.profile.SID" >
                          
                          <small v-if="form.errors.has('user.profile.SID')" v-text="form.errors.get('user.profile.SID')" class="text-danger" >身分證號</small>
                             
                        </div>
                   </div>
                   <div  class="col-sm-3"> 
                       <div class="form-group">
                            <label>生日</label>
                            <div>
                                <date-picker  :date="dob" :option="datePickerOption"></date-picker>
                            </div>
                            <input type="hidden" name="user.profile.dob" class="form-control" v-model="form.user.profile.dob"  >
                            <small class="text-danger" v-if="form.errors.has('user.profile.dob')" v-text="form.errors.get('user.profile.dob')"></small>
                        </div>
                   </div>
                </div>
                <div class="row" v-if="user_checked">
                   <div  class="col-sm-3"> 
                       
                       <div class="form-group">
                            <label>報名日期</label>
                            <div>
                                <date-picker  :date="signupDate" :option="datePickerOption"></date-picker>
                            </div>
                            <input type="hidden" name="signup.date" class="form-control" v-model="form.signup.date"  >
                            <small class="text-danger" v-if="form.errors.has('signup.date')" v-text="form.errors.get('signup.date')"></small>
                        </div>
                   </div>  
                   <div class="col-sm-6"> 
                       <div class="form-group">                          
                          <label>報名課程</label>

                         <combination-select :with_course="combinationSettings.withCourse"
                          @ready="onCombinationReady" @course-changed="setCourse"></combination-select>

                          <input type="hidden" name="signup.course_id" class="form-control" v-model="form.signup.course_id"  >
                          <small v-if="form.errors.has('signup.course_id')" v-text="form.errors.get('signup.course_id')" class="text-danger" >身分證號</small>
                             
                        </div>
                   </div>
                </div>
                <div class="row" v-if="user_checked">
                   <div  class="col-sm-9"> 
                       
                       <div class="form-group">
                            <label>折扣優惠</label>
                            <div>
                               <toggle :items="discountOptions"   default_val="0" @selected=setDiscount></toggle>
                           </div>
                        </div>
                   </div>  
                   
                </div>
                <div class="row" v-show="user_checked">
                    <div class="col-sm-6">
                        <button type="submit"  class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button class="btn btn-default" @click.prevent="canceled">取消</button>
                    </div>



                </div> 
            </form>
        </div>
    </div>


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
    import UserSelector from '../../components/user/selector.vue'
    export default {
        name: 'NewUserSignup',
        components: {
            'user-checker':UserChecker,
            'user-selector':UserSelector
        },
        props: {
            user_valid:{
               type: Boolean,
               default: false
            },
        },
        data() {
            return {
                title:Helper.getIcon('Signups')  + '  新學員報名',
                form : new Form({
                    user:{
                       email:'',
                       phone:'',
                       profile:{
                          fullname:'',
                       }
                    },
                    signup:{}
                }),
                formSubmitting:false,
                user_checked: false,
                loaded:false,

               
                dob: {
                    time: ''
                },
                dobError:false,

               
                genderOptions:Helper.genderOptions(),
                datePickerOption: Helper.datetimePickerOption(),
                
                discountOptions:[],
                signupDate: {
                    time: Helper.todayString()
                },
                

                userList:[],
                showUserList:false,

                userCheckerSettings:{
                    version:0,
                    status:0
                },
                modalSettings:{
                   width:1200,
                },
                combinationSettings:{
                   withCourse:true,
                },

            }
        },
        watch:{
            signupDate: {
              handler: function () {
                this.form.signup.date=this.signupDate.time
                this.clearErrorMsg('signup.date')
              },
              deep: true
            },
            dob: {
              handler: function () {
                  this.form.user.profile.dob=this.dob.time
                  this.clearErrorMsg('user.profile.dob')
              },
              deep: true
            },
        },
        beforeMount() {
           this.init()
        },
        methods: {
            
            init() {
                this.form = new Form({
                    user:{
                       email:'',
                       phone:'',
                       profile:{
                          fullname:'',
                       }
                    },
                    signup:{
                       date:{}
                    }
                })
                this.formSubmitting=false
                this.user_checked= false
                this.loaded=false

             
                this.dob={
                    time: ''
                }
                this.dobError=false

              
                
                this.discountOptions=[]
                this.signupDate= {
                    time: Helper.todayString()
                }

                this.userList=[]
                this.showUserList=false

                let create=Signup.newUserCreate()
                create.then(data=>{
                     this.form.user=data.user
                     this.form.signup=data.signup
                     this.loadDiscountOptions(data.discountOptions)
                }).catch(error =>{
                     Helper.BusEmitError(error)
                })
                
            },
            canceled(){
                this.init()
            },
            onUserChecked(user){
                this.user_checked=true
                this.form.user.phone=user.phone
                this.form.user.email=user.email
                this.form.user.profile.fullname=user.profile.fullname

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
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onCombinationReady(params){
                this.setCourse(params.course)

            },
            setGender(val) {
                this.form.user.profile.gender = val;
            },
            setCourse(val){
                this.form.signup.course_id=val
                 this.clearErrorMsg('signup.course_id')
            },
            setDiscount(val) {
                this.form.signup.discount_id = val;
            },
            loadDiscountOptions(options){
                this.discountOptions=options
                let noDiscount={ text:'無' , value:'0' }
                this.discountOptions.splice(0, 0, noDiscount);
            },    
            onSubmit() {
                this.formSubmitting=true
                this.userCheckerSettings.status+=1
            },
            submitForm() {

                let store=Signup.newUserStore(this.form)
                    .then(data => {
                       Helper.BusEmitOK()
                       this.$emit('saved',data)                            
                    })
                    .catch(error => {
                        if(error.status==422){
                           this.dobError=this.form.errors.has('user.profile.dob') 
                        }else{
                            Helper.BusEmitError(error,'存檔失敗') 
                        }
                       
                    })
            },
         




        },

    }
</script>