<template>
<div>
   
    <div v-if="loaded" class="panel panel-default">
        <div class="panel-heading">           
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>  
            <div>
                <button @click="onCanceled" class="btn btn-default btn-sm" >
                    <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                    返回
                </button>
            </div>         
        </div>
        <div class="panel-body">
            
            <signup-user :user_options="userOptions" @user-changed="onUserChanged"></signup-user>

            <course-list ref="courseList" :init_courses="selectedCourses" :can_remove="selectedCourses.length>1" :can_add="userSelected"
                @course-changed="onCourseChanged">
            </course-list>

            <form  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form">
                <bill-inputs v-if="courseSelected" v-show="userSelected" ref="billInputs"   :form="form" :payways="payways"
                  @clear-error="clearErrorMsg">
                </bill-inputs> 
               <!-- <div class="row" v-show="canSubmit">
                  <div class="col-sm-6">
                     <button type="submit"  class="btn btn-success" >確認送出</button>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <button class="btn btn-default" @click.prevent="canceled">取消</button>
                  </div>

               </div> -->
            </form> 
        </div>
    </div>
</div>    
</template>

<script>
   
    import CourseList from '../../components/signup/course/list.vue'
    import SignupUser from '../../components/signup/user/user.vue'
    import BillInputs from '../../components/bill/inputs.vue'
    export default {
        name: 'SignupCreate',
        components: {
            'course-list':CourseList,
            'signup-user':SignupUser,
            'bill-inputs':BillInputs,
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
                loaded:false,

                title:Helper.getIcon('Signups')  + '  新增報名表',
               // center_id:0,

                selectedUser:null,
                userOptions:[],
                userViewSettings:{
                    can_back:true
                },
               
                selectedCourses:[],
                
                form:{}
             
            }
        },
        computed:{
            userSelected(){
                if(!this.selectedUser) return false
                if(!this.selectedUser.value)  return false
                return true
            },
            courseSelected(){
                return this.selectedCourses.length > 0
            }
        },
        watch: {
            selectedUser(){
               
                // let user_id=0
                // if(this.selectedUser) {
                //      user_id=Helper.tryParseInt(this.selectedUser.value)
                // } 
                
                // this.signup.user_id=user_id

                // this.clearErrorMsg('signup.user_id') 
            },
            
    
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                this.fetchData()
            },
            fetchData() {
               
                let getData=Signup.create(this.course_id,this.user_id)
                     
                getData.then(data => {
                    // let signup=data.signup
                    // this.date.time=signup.date

                    // this.signup=signup;

                    

                    if(data.course){
                       
                        let course=new Course(data.course)
                        this.selectedCourses=[course]
                        //this.center_id=parseInt(course.center_id)
                        this.userOptions=data.userOptions 
                    }
                    

                    this.form=new Form({
                        signups:[data.signup],
                        bill: Object.assign({}, data.bill) ,
                        tuition: Object.assign({}, data.tuition) ,
                       
                    })
                     
                    this.payways=data.payways.filter((payway)=>{
                        return parseInt(payway.value) !=1 
                    })

                    this.loaded=true
                   
                })
                .catch(error=> {
                    Helper.BusEmitError(error)                        
                })
                
            },
            onUserChanged(user){
                this.selectedUser=Object.assign({}, user)
            },
            getUserId(){
                if(this.userSelected) return parseInt(this.selectedUser.value)
                return 0
            },
            newUserSignupUrl(){
                let url= '/signups/new-user'
                if(this.course_id) url +='?course=' + this.course_id
                return url
            },
            clearUser(){
                this.selectedUser=null
            },
            onCourseChanged(){
                this.form.bill.total=this.$refs.courseList.getTotal()

                let courses = this.$refs.courseList.getCourses()
                this.selectedCourses= courses.slice(0)

                this.form.signups=[]

                this.selectedCourses.forEach((course)=>{
                    let signup=Signup.init(this.getUserId(),course)
                    this.form.signups.push(signup)
                })

                

                this.$refs.billInputs.fetchData()
                
            },
            clearErrorMsg(name){
                alert(name)
            },
            onCanceled(){
                this.$emit('canceled')
            },
            onSaved(){
                this.$emit('saved')
            },
            
           
            
            
        },

    }
</script>