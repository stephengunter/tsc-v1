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
                   <div v-if="form.signup.user_id" class="col-sm-12">
                       <user-view ref="userview" :user_id="form.signup.user_id"
                          :can_back="userViewSettings.can_back" @canceled="clearUser">

                       </user-view>
                   </div>
                   <div v-else class="col-sm-6">
                       <label>姓名</label>
                        <div class="input-group">
                            <drop-down  v-model="selectedUser" :options="userOptions" label="text" ></drop-down>
                            <small class="text-danger" v-if="form.errors.has('signup.user_id')" v-text="form.errors.get('signup.user_id')"></small>
                        
                            <a v-show="!selectedUser" :href="newUserSignupUrl()" class="input-group-addon" >新學員報名</a>
                        </div>
                   </div>
                      
                    <small class="text-danger" v-if="form.errors.has('signup.user_id')" v-text="form.errors.get('signup.user_id')" ></small>
              
               </div>
               <div class="row">
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>報名日期</label>
                            <div>
                                <date-picker :option="datePickerOption" :date="date" ></date-picker>
                            </div>
                            <input type="hidden" name="signup.date" class="form-control" v-model="form.signup.date"  >
                            <small v-if="form.errors.has('signup.date')" v-text="form.errors.get('signup.date')" class="text-danger"></small>
                        </div>  
                   </div>
                   <div class="col-sm-9">
                        <div class="form-group"> 
                            <courses-table :courses="selectedCourses" :can_remove="courseTableSettings.can_remove" >
                            </courses-table>
                          
                        </div>
                         <small v-if="form.errors.has('signup.course_id')" v-text="form.errors.get('signup.course_id')" class="text-danger"></small>
                   </div>
               </div>
              
                <bill-inputs ref="billinputs" v-if="form.signup.user_id" :form="form" :center_id="center_id" 
                   :payways="payways" :date="form.signup.date" :can_edit="billInputSettings.can_edit" >
                 
                </bill-inputs> 
              
               
                <div v-show="canSubmit" class="row" >
                    <div  class="col-sm-6">
                        <button type="submit"  class="btn btn-success" :disabled="!canSubmit">確定</button>
                      
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-default" @click.prevent="canceled">取消</button>
                    </div>  

                </div>    
                  
           </form>
        </div>
    </div>
    
</template>
<script>
    import SignupUserView from './user/view.vue'
    import CoursesTable from './courses-table.vue' 
    import BillInputs from '../../components/bill/inputs.vue'

    export default {
        name: 'CreateSignupByCourse',
        components: {
            'user-view':SignupUserView,
            'courses-table':CoursesTable,
            'bill-inputs':BillInputs
        },
        props: {
            course_id:{
               type: Number,
               default: 0
            },
            can_pay:{
               type: Boolean,
               default: true
            },
        },
        data() {
            return {
                title:Helper.getIcon('Signups')  + '  新增報名表',
                initialized:false,

                center_id:0,

                selectedUser:null,
               
                selectedCourses:[],
                courseTableSettings:{
                    can_remove:false
                },

                userViewSettings:{
                    can_back:true
                },

                billInputSettings:{
                    can_edit:true
                },
               
               
                form: {},

                userOptions:[],

                datePickerOption:{},
                date: {
                    time: ''
                },
                payways:[],

            }
        },
        computed: {
           
            canSubmit(){
                if(!this.form.signup.user_id) return false

                return true
            },
        },
        watch: {
            selectedUser(){
                let user_id=0
                if(this.selectedUser) {
                     user_id=Helper.tryParseInt(this.selectedUser.value)
                } 
                
                this.form.signup.user_id=user_id

                this.clearErrorMsg('signup.user_id') 
            },
            date: {
              handler: function () {
                 
                  this.form.signup.date=this.date.time

                 
                  this.clearErrorMsg('signup.date')
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
                    this.date.time=signup.date

                    let course=new Course(data.course)

                    this.selectedCourses=[course]

                    this.center_id=parseInt(course.center_id)
                    

                    this.form=new Form({
                        signup:signup,
                        bill:data.bill,
                        tuition:data.tuition,
                       
                    })

                    
                    this.userOptions=data.userOptions  
                    this.payways=data.payways

                    this.initialized=true
                   
                })
                .catch(error=> {
                    Helper.BusEmitError(error)                        
                })
                
            },
            clearUser(){
                this.form.signup.user_id=0
                this.selectedUser=null
            },
            newUserSignupUrl(){
                let url= '/signups/new-user'
                if(this.course_id) url +='?course=' + this.course_id
                return url
            },
            onSubmit() {
                //只能報單一課程,必須繳費

                this.form.signup.tuition=this.form.bill.amount

                let store=Signup.store(this.form)
                
                store.then(data => {

                    Helper.BusEmitOK()
                    this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
                
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            canceled(){
                this.$emit('canceled')
            },
            
            




        },

    }
</script>