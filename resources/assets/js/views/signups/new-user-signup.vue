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
                <user-inputs ref="userinputs"  :form="form" @checked="onUserChecked" @check-failed="onUserCheckFailed">

                </user-inputs>
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
                   
                   <div  class="col-sm-2">
                        <div class="form-group"> 
                            <label>是否繳費</label>
                            <div>
                                <toggle :items="boolOptions"   :default_val="form.pay" @selected="setPay"></toggle>
                            </div>
                        </div>  
                   </div>
                </div>
                <div class="row" v-if="user_checked" v-show="isPay">
                    <div  class="col-sm-12"> 
                        <div class="form-group">
                            <label>報名課程</label>
                            <button @click.prevent="selectCourseModal.show=true" type="button" class="btn btn-info btn-sm" >
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                            <table v-show="selectedCourses.length" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>課程編號</th>
                                        <th>課程名稱</th>
                                        <th>上課時間</th>
                                        <th>課程期間</th>
                                        <th>課程費用</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(course,index) in selectedCourses" :key="index" >
                                        <td>{{ course.number }}</td>
                                        <td>{{ course.fullname }}</td>
                                        <td>{{ course.classTimesText }}</td>
                                        <td>{{ course.period }}</td>
                                        <td>{{ course.tuition | formatMoney}}</td>
                                        <td>
                                            <button @click.prevent="removeItem(course.id)" class="btn btn-danger btn-xs">
                                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 v-show="total>0">合計：{{ total | formatMoney }}元</h3> 
                        </div>
                    </div>  
                </div>
                <bill-inputs  v-if="center_id" :form="form" :center_id="center_id" :payways="payways"
                   @discount-selected="onDiscountSelected" @clear-error="clearErrorMsg">
                </bill-inputs>
               
                <div class="row" v-show="user_checked">
                    <div class="col-sm-6">
                        <button type="submit"  class="btn btn-success" :disabled="!canSubmit">確認送出</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button class="btn btn-default" @click.prevent="canceled">取消</button>
                    </div>

                </div> 
            </form>
        </div>
    </div>

    <modal  :show="selectCourseModal.show" effect="fade"  :width="selectCourseModal.width">
        <div slot="modal-header" class="modal-header">
            <button id="close-button" type="button" class="close" @click="selectingCourse=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
             </button>
             <h3>
                 請選擇課程
             </h3>
          </div>  
        <div slot="modal-body" class="modal-body">
            <combination-select v-if="selectingCourse"  :search_params="search_params"
                @ready="onCombinationReady">                           
            </combination-select>
        </div>
        <div slot="modal-footer" class="modal-footer" >
            <button type="button" class="btn btn-success" @click.prevent="onCourseSelected">確定</button>
        </div>
    </modal>
    <modal :showBtn="true"  :show="courseConfirmModal.show"  effect="fade" :width="courseConfirmModal.width">
        
        <div slot="modal-header" class="modal-header modal-header-danger">
        
        <button id="close-button" type="button" class="close" data-dismiss="modal" @click.prevent="courseConfirmModal.show=false">
            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
        </button>
            <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
        </div>
        <div slot="modal-body" class="modal-body">
            <h3> {{  courseConfirmModal.msg }} </h3>
        </div>
        <div slot="modal-footer" class="modal-footer" >
            <button type="button" class="btn btn-default" @click.prevent="courseConfirmModal.show=false">確定</button>
        </div>
        
    </modal>
    <modal :showbtn="false"  :show="userListModal.show" effect="fade" :width="userListModal.width">
        
          <div slot="modal-header" class="modal-header modal-header-danger">
             <button id="close-button" type="button" class="close" data-dismiss="modal" @click="userListModal.show=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
             </button>
             <h3>
                 <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                 相同資料的使用者已經存在
             </h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <user-selector :users="userListModal.users" @selected="onUserSelected"></user-selector>
        </div>
    </modal>
</div>       
</template>

<script>
    import UserInputs from '../../components/signup/user-inputs.vue'
    import UserSelector from '../../components/user/selector.vue'
    import BillInputs from '../../components/bill/inputs.vue'
    export default {
        name: 'NewUserSignup',
        components: {
            'user-inputs':UserInputs,
            'user-selector':UserSelector,
            'bill-inputs':BillInputs
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
                    pay:1
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

                
                boolOptions:Helper.boolOptions(),

                datePickerOption: Helper.datetimePickerOption(),
                
               
                signupDate: {
                    time: Helper.todayString()
                },

                userListModal:{
                    show:false,
                    width:1200,
                   
                    users:[]
                },

               
                selectCourseModal:{
                    show:false,
                    width:600,
                    course_id:0
                },

                courseConfirmModal:{
                    show:false,
                    width:600,  
                    course:null
                },

                course_ids:[],
                selectedCourses:[],

               
                

            }
        },
        computed: {
            
            selectingCourse(){
                return Helper.isTrue(this.selectCourseModal.show)
            },
            hasCourse(){
                if(!this.form.signup) return false
                return Helper.isTrue(this.form.signup.course_id)
            },
            center_id(){
                if(!this.selectedCourses.length) return 0
                return parseInt(this.selectedCourses[0].center_id)
            },
            total(){
                if(!this.selectedCourses.length) return 0
                let tuitions = this.selectedCourses.map((course) => {
                    return Number(course.tuition)
                })
                return tuitions.reduce((prev, curr) => prev + curr)
                
            },
            isPay(){
                return Helper.isTrue(this.form.pay)
            },
            canSubmit(){
                if(this.isPay){
                    return Number(this.total) > 0
                }else{
                    return true
                }
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
            total(){
                
                this.form.bill.total= this.total
              
            }
            
        },
        beforeMount() {
           this.init()
        },
        methods: {
            
            init() {
               
                this.formSubmitting=false
                this.user_checked= false
                this.loaded=false
              
                
                this.discountOptions=[]
                this.signupDate= {
                    time: Helper.todayString()
                }

               

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
                        bill:data.bill,
                        tuition:data.tuition,
                        pay:1
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
            onUserCheckFailed(users){
                this.formSubmitting=false
                this.userListModal.users= users
                if(users.length){
                   this.userListModal.show=true
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
               this.selectCourseModal.course_id=course_id
            },
            courseExist(id){
                if(this.selectedCourses.length < 1 ) return false
                let item=this.selectedCourses.find((course)=>{
                    return course.id==id
                })

                if(item) return true
                return false
            },
            onCourseSelected(){
                this.selectCourseModal.show=false

                let id=this.selectCourseModal.course_id
                if(this.courseExist(id)) return false

                let getCourse=Course.show(id)
                getCourse.then(data=>{
                    
                    let course=new Course(data.course)

                    let canSignup=Helper.isTrue(course.canSignup)
                    if(!canSignup){
                        this.courseConfirmModal.msg=course.fullname +  ' 已停止報名'
                        this.courseConfirmModal.show=true

                        return false
                    }

                    this.selectedCourses.push(course)

                   
                    
                }).catch(error =>{
                     Helper.BusEmitError(error)
                })
            },
            removeItem(id){
                let index=this.selectedCourses.findIndex((course)=>{
                    return course.id==id
                })

                this.selectedCourses.splice(index,1)
            },
            setPay(val){
                this.form.pay=val
            },
           
            onDiscountSelected(discount){
                this.form.signup.discount_id=discount.id
                this.form.signup.tuition=Helper.formatMoney(discount.tuition,true)                
            },
                      
            onSubmit() {
                
                 this.formSubmitting=true
                 this.$refs.userinputs.checkUser()
                // this.userCheckerSettings.status+=1
            },
            submitForm() {
               
                let store=Signup.newUserStore(this.form)
                    .then(data => {
                       Helper.BusEmitOK()
                       if(this.isPay){

                       }else{
                           Helper.redirect('/users/' + data.id)
                       }


                       


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