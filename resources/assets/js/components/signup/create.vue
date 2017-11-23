<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">           
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>  
            <div>
                <button   @click="canceled" class="btn btn-default btn-sm" >
                     <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                     返回
                </button>
            </div>         
        </div>
        <div class="panel-body">
            <form v-if="initialized" @submit.prevent="onSubmit" class="form">
               <div class="row">
                   <div v-if="false" class="col-sm-3">
                        <div class="form-group"> 
                           <label>報名日期</label>
                            <div>
                                <date-picker :option="datePickerOption" :date="date" ></date-picker>
                            </div>
                            <input type="hidden" name="signup.date" class="form-control" v-model="form.signup.date"  >
                            <small v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')" class="text-danger"></small>
                        </div>  
                   </div>
                   <div class="col-sm-9">
                        <div v-if="course_id" class="form-group"> 
                           <label>報名課程</label>
                           <input class="form-control" type="text"  :value="courseName" disabled>
                        </div>
                        <div v-else class="form-group"> 
                            <label>報名課程</label>
                            <combination-select 
                              @ready="onCombinationReady">
                            </combination-select>

                            <small v-if="form.errors.has('signup.course_id')" v-text="form.errors.get('signup.course_id')" class="text-danger" >身分證號</small>
                        </div>  
                   </div>
               </div>
               <div class="row">
                   <div class="col-sm-6">
                        <div v-if="user_id" class="form-group"> 
                            <label>姓名</label>
                            <input class="form-control" type="text"  :value="user.profile.fullname" disabled>
                            <small class="text-danger" v-if="form.errors.has('signup.user_id')" v-text="form.errors.get('signup.user_id')"></small>

                        </div>
                        <div v-else class="form-group"> 
                            <label>姓名</label>
                            <div class="input-group">
                                <drop-down  v-model="selectedUser" :options="userOptions" label="text" ></drop-down>
                                <small class="text-danger" v-if="form.errors.has('signup.user_id')" v-text="form.errors.get('signup.user_id')"></small>
                            
                                <a v-show="!hasUser" :href="newUserSignupUrl()" class="input-group-addon" >新學員報名</a>
                            </div>
                           
                        </div>  
                   </div>
                   <div class="col-sm-3">
                        <div class="form-group"> 
                            <label>是否繳費</label>
                            <div>
                                <toggle :items="boolOptions"   :default_val="pay" @selected="setPay"></toggle>
                            </div>
                        </div>  
                   </div>
                      
                   
               </div>
               <div v-if="user" class="row">
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>性別</label>
                           
                            <input class="form-control" type="text"  :value="$options.filters.genderText(user.profile.gender)" disabled>
                           
                        </div>  
                   </div>
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>生日</label>
                           <input class="form-control" type="text"  :value="user.profile.dob" disabled>
                       
                         </div>  
                   </div>
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>身分證號</label>
                           
                            <input class="form-control" type="text"  :value="user.profile.SID" disabled>
                        
                        </div>  
                   </div>
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>電話</label>
                           <div class=" form-inline">
                              <input class="form-control" type="text"  :value="user.phone" disabled>
                              &nbsp;&nbsp;
                              <button @click.prevent="onEditUser" class="btn btn-primary btn-xs" >
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                              </button>
                           </div>
                         </div>  
                   </div>
               </div>
               <div v-if="edittingUser" class="row">
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>性別</label>
                           <div>
                             <toggle :items="genderOptions"   :default_val="editUserForm.user.profile.gender" @selected="setGender"></toggle>
                           </div>
                        </div>  
                   </div>
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>生日</label>
                            <div>
                                <date-picker  :date="dob" :option="datePickerOption"></date-picker>
                            </div>
                         </div>  
                   </div>
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>身分證號</label>
                           <input type="text" name="user.profile.SID" class="form-control" v-model="editUserForm.user.profile.SID" @keydown="clearEditUserError('user.profile.SID')"  >
                           <small class="text-danger" v-if="editUserForm.errors.has('user.profile.SID')" v-text="editUserForm.errors.get('user.profile.SID')"></small>
                        </div>  
                   </div>
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>電話</label>
                           <div class=" form-inline">
                              <input type="text" name="user.phone" class="form-control" v-model="editUserForm.user.phone" @keydown="clearEditUserError('user.phone')">
                              <small class="text-danger" v-if="editUserForm.errors.has('user.phone')" v-text="editUserForm.errors.get('user.phone')"></small>
                               &nbsp;&nbsp;
                                 <button class="btn btn-success btn-xs" @click.prevent="saveUser">
                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                 </button> 
                                 <button class="btn btn-default btn-xs" @click.prevent="cancelEditUser">
                                    <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>               
                                 </button> 
                           </div>
                         </div>  
                   </div>
                </div>
               
                <div v-if="needSelect" class="row">
                   <div class="col-sm-12" >
                       <sub-course-selector :courses="subCourses"
                        :show_submit="selectorSettings.show_submit" :version="selectorSettings.version"
                         :default_selected="selectorSettings.default_selected"
                         @submit-courses="onSubCourseSelected" >
                       </sub-course-selector>
                   
                   </div> 
                </div> 
                <div v-if="isPay" class="row">
                   <div class="col-sm-12">
                        <div class="form-group"> 
                            <label>折扣優惠</label>
                            <discounts-selector :course_id="course_id" :discount_id="discount_id"
                              @selected="onDiscountSelected">
                            </discounts-selector>
                          
                        </div>  
                   </div>
                   
                </div>  
                <div v-if="isPay" class="row">
                    <div  class="col-sm-3">
                        <div class="form-group"> 
                            <label>學費</label>
                            <input disabled type="text" name="signup.tuition" class="form-control" v-model="form.signup.tuition" @keydown="clearErrorMsg('signup.tuition')"  >
                            <small class="text-danger" v-if="form.errors.has('signup.tuition')" v-text="form.errors.get('signup.tuition')"></small>
                        </div>  
                    </div>
                    <div  class="col-sm-3">    
                        <div class="form-group"> 
                            <label>材料費</label>
                            <input disabled type="text" name="signup.cost" class="form-control" v-model="form.signup.cost" @keydown="clearErrorMsg('signup.cost')"  >
                            <small class="text-danger" v-if="form.errors.has('signup.cost')" v-text="form.errors.get('signup.cost')"></small>
                        </div> 
                    </div>
                    <div  class="col-sm-3">    
                        <div class="form-group"> 
                            <label>合計</label>
                            <input disabled type="text" :value="total" name="signup.cost" class="form-control"  >
                            
                        </div> 
                    </div>
                </div>  
                <div class="row" >
                    <div  class="col-sm-6">
                        <button type="submit"  class="btn btn-success" :disabled="!canSubmit">確定</button>
                        <small class="text-danger" v-if="subCourseError" >請選擇課程</small>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-default" @click.prevent="canceled">取消</button>
                    </div>  

                </div>    
                  
           </form>
        </div>
    </div>
    
</template>
<script>
    import SubCourseSelector from '../../components/course/sub/selector.vue'
    import DiscountSelector from './discounts-selector.vue'
    export default {
        name: 'CreateSignup',
        components: {
            'sub-course-selector':SubCourseSelector,
            'discounts-selector':DiscountSelector
        },
        props: {
            course_id:{
               type: Number,
               default: 0
            },
            user_id:{
               type: Number,
               default: 0
            },
        },
        data() {
            return {
                title:Helper.getIcon('Signups')  + '  新增報名表',
                initialized:false,

                subCourses:[],

                selectedUser:null,
                user:null,

                edittingUser:false,
                genderOptions:[],
                dob:{
                  time: ''
                },
                editUserForm:{},
                

                
                courseName:'',
                discount_id:0,
                form: new Form({
                   signup:{}
                }),

                userOptions:[],
                courseOptions:[],
                boolOptions:Helper.boolOptions(),
                

                datePickerOption:{},
                date: {
                    time: ''
                },

               
                selectorSettings:{
                   default_selected:[],
                   show_submit:false,
                   version:0,
                },

                subCourseError:false,

                pay:0

            }
        },
        computed: {
            hasUser(){
                if(!this.user) return false
                if(!this.user.id) return false

                return true
            },
            canSubmit(){
                if(this.edittingUser) return false
                if(!this.user) return false

                return true
            },
            needSelect(){
                return this.subCourses.length > 0  
            },
            isPay(){
                return Helper.isTrue(this.pay)
            },
            total(){
                return Number(this.form.signup.tuition) + Number(this.form.signup.cost)
            }
        },
        watch: {
            selectedUser: function () {
                this.user=null
                this.edittingUser=false
                this.getUser()               
            },
            date: {
              handler: function () {
                  this.form.signup.date=this.date.time
                  this.clearErrorMsg('signup.date')
              },
              deep: true
            },
            dob: {
              handler: function () {
                  this.editUserForm.user.profile.dob=this.dob.time        
              },
              deep: true
            },
    
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.fetchData() 
                this.datePickerOption=Helper.datetimePickerOption()

            },
            fetchData() {
                let getData=Signup.create(this.course_id,this.user_id)
                     
                getData.then(data => {
                    let signup=data.signup
                    signup.cost=Helper.formatMoney(signup.cost,true)
                   
                    this.subCourses=data.subCourses
                    this.date.time=signup.date
                    this.selectorSettings.default_selected=signup.sub_courses
                    

                    this.form=new Form({
                        signup:signup
                    })

                    if(data.course){
                        this.courseName= Course.getFormatedCourseName(data.course,true)                   
                    }

                    if(data.user){
                        this.user=data.user
                    }

                    

                    this.courseOptions=data.courseOptions
                    this.userOptions=data.userOptions   

                   

                    this.initialized=true
                   
                })
                .catch(error=> {
                    Helper.BusEmitError(error)                        
                })
                
            },
            newUserSignupUrl(){
                let url= '/signups/new-user'
                if(this.course_id) url +='?course=' + this.course_id
                return url
            },
            getUser(){
                let user_id=0
                if(this.selectedUser) user_id=this.selectedUser.value

                if(!user_id){
                    this.user =null
                    return
                } 

                let user=User.edit(user_id)   
                user.then(data => {
                    this.user = data.user
                    this.clearErrorMsg('signup.user_id')                    
                })
                .catch(error => {
                    Helper.BusEmitError(error)                                        
                })
            },
            setUser(user){
               if(user){
                    this.selectedUser = {
                       text: user.profile.fullname,
                       value: user.id
                    }
               }
            },

            onCombinationReady(course_id){
                
                this.onCourseSelected(course_id)
            },
            onCourseSelected(val){
             
               this.form.signup.course_id=val
               this.clearErrorMsg('signup.course_id')
               this.clearErrorMsg()

            },
            setPay(val){
                this.pay=val
            },
            
            setNetSignup(val){
                this.form.signup.net_signup=val
            },     
            
            onSubCourseSelected(selectedIds){
                this.form.signup.sub_courses=selectedIds
                if(selectedIds.length){
                   this.subCourseError=false
                   this.submitForm()
                }else{
                    this.subCourseError=true
                }
            },
            onDiscountSelected(discount){
                this.form.signup.discount_id=discount.id
                this.form.signup.tuition=Helper.formatMoney(discount.tuition,true)
                
            },
            onSubmit() {
              
                if(this.course_id){
                    if(this.selectedUser){
                       this.form.signup.user_id = this.selectedUser.value
                    }else{
                       this.form.signup.user_id =''
                    }
                }

               let errors={}
               let signup=this.form.signup
             
               if(!signup.course_id) {
                  errors['signup.course_id']=['必須選擇報名課程']
               }
               if(!signup.user_id) {
                  errors['signup.user_id']=['必須選擇姓名']
               }
               
               this.form.onFail(errors)
               if(this.form.errors.any()){
                  return false
               }

               if(this.needSelect){
                  this.selectorSettings.version +=1
               }else{
                  this.submitForm()
               }

            
                
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            submitForm() {

                let store=Signup.store(this.form)
                
                store.then(data => {

                    Helper.BusEmitOK()
                    this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
            canceled(){
                this.$emit('canceled')
            },
            onEditUser(){
             
                this.editUserForm=new Form({
                    user:this.user
                })
                this.dob.time=this.user.profile.dob
                this.genderOptions= Helper.genderOptions()

                this.user=null 
                this.edittingUser=true

            },
            setGender(val) {
                this.editUserForm.user.profile.gender = val;
            },
            clearEditUserError(name){
                 this.editUserForm.errors.clear(name)
            },
            cancelEditUser(){
                 this.getUser()
                 this.edittingUser=false
            },
            saveUser(){

                let updateUser=Signup.updateUser(this.editUserForm)
                
                updateUser.then(user => {
                   this.user = user
                   this.edittingUser=false
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            }




        },

    }
</script>