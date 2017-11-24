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
                              <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected="setGender"></toggle>
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
                   <div class="col-sm-7"> 
                       <div class="form-group">                          
                            <label>報名課程</label>

                            <combination-select  :search_params="search_params"
                               @ready="onCombinationReady">                           
                            </combination-select>
                            

                            <input type="hidden" name="signup.course_id" class="form-control" v-model="form.signup.course_id"  >
                            <small v-if="form.errors.has('signup.course_id')" v-text="form.errors.get('signup.course_id')" class="text-danger" >身分證號</small>
                                
                        </div>
                   </div>
                   <div v-if="hasCourse" class="col-sm-2">
                        <div class="form-group"> 
                            <label>是否繳費</label>
                            <div>
                                <toggle :items="boolOptions"   :default_val="pay" @selected="setPay"></toggle>
                            </div>
                        </div>  
                   </div>
                </div>
                <pay-inputs  v-if="isPay"  :form="form" :payways="payways"
                   @discount-selected="onDiscountSelected" @clear-error="clearErrorMsg">
                </pay-inputs>
               
                <div class="row" v-show="user_checked">
                    <div class="col-sm-6">
                        <button type="submit"  class="btn btn-success" :disabled="!hasCourse">確認送出</button>
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
    import PayInputs from '../../components/signup/pay-inputs.vue'
    export default {
        name: 'NewUserSignup',
        components: {
            'user-checker':UserChecker,
            'user-selector':UserSelector,
            'pay-inputs':PayInputs
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
                    signup:{},
                    tuition:{},
                    pay:0
                }),
                formSubmitting:false,
                user_checked: false,
                loaded:false,

                search_params:{
                    term:0,
                    center:0,
                    parent:0,
                    sub:0,
                    reviewed:1
                },

                pay:0,
                boolOptions:Helper.boolOptions(),
                
               
                dob: {
                    time: ''
                },
                dobError:false,

               
                genderOptions:Helper.genderOptions(),
                datePickerOption: Helper.datetimePickerOption(),
                
               
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
                

            }
        },
        computed: {
            hasCourse(){
                if(!this.form.signup) return false
                return Helper.isTrue(this.form.signup.course_id)
            },
            isPay(){
                return Helper.isTrue(this.pay)
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
                    },

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

                this.fetchData()
                
            },
            fetchData(){
                let create=Signup.newUserCreate()
                create.then(data=>{
                    
                    let signup=data.signup
                    signup.cost=Helper.formatMoney(signup.cost,true)

                    this.form=new Form({
                        user:data.user,
                        signup:signup,
                        tuition:data.tuition,
                        pay:0
                    })
                    this.payways=data.payways
                    
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

            onCombinationReady(course_id){
               alert(course_id)
                this.setCourse(course_id)

            },
            setGender(val) {
                this.form.user.profile.gender = val;
            },
            setPay(val){
                this.pay=val
            },
            setCourse(val){
              
                this.form.signup.course_id=val
                this.clearErrorMsg('signup.course_id')
            },
            onDiscountSelected(discount){
                this.form.signup.discount_id=discount.id
                this.form.signup.tuition=Helper.formatMoney(discount.tuition,true)                
            },
           
            onSubmit() {
                this.formSubmitting=true
                this.userCheckerSettings.status+=1
            },
            submitForm() {
                this.form.pay=this.pay
                let store=Signup.newUserStore(this.form)
                    .then(data => {
                       Helper.BusEmitOK()
                       //this.$emit('saved',data)                            
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