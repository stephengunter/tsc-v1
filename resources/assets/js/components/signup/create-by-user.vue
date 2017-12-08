<template>
   <div>
      <h3>線上報名</h3>
      <user-view ref="userview" :user_id="user_id"></user-view>
      <div class="panel panel-default">
         <div class="panel-heading">           
            <span class="panel-title">
               <h4 v-html="title"></h4> 
            </span>  
            <button @click.prevent="courseSelector.show=true" class="btn btn-primary btn-sm" >
               <span class="glyphicon glyphicon-plus"></span> 新增
            </button>         
         </div>
         <div class="panel-body">
            <div class="row">
               <div  class="col-sm-12"> 
                  <courses-table v-show="selectedCourses.length"  :courses="selectedCourses"
                    @remove-course="removeCourse">
                  </courses-table>
                  <h3 v-show="total>0">合計：{{ total | formatMoney }}元</h3> 
               </div>
            </div>  
            <form  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form">
               <bill-inputs  v-if="center_id" :form="form" :center_id="center_id" :payways="payways"
                  @clear-error="clearErrorMsg">
               </bill-inputs> 
               <div class="row" v-show="canSubmit">
                  <div class="col-sm-6">
                     <button type="submit"  class="btn btn-success" >確認送出</button>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <button class="btn btn-default" @click.prevent="canceled">取消</button>
                  </div>

               </div>
            </form>  
         </div>   
      </div>  

      <course-selector :show="courseSelector.show" @selected="onCourseSelected" @cancel="courseSelector.show=false">

      </course-selector>

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
      
   </div>
   
    
</template>
<script>
   import SignupUserView from './user/view.vue'
   import CoursesTable from './courses-table.vue'
   import CourseSelector from './course-selector.vue'
   import BillInputs from '../../components/bill/inputs.vue'

   export default {
      name: 'CreateSignupByUser',
      components: {
         'user-view':SignupUserView,
         'courses-table':CoursesTable,
         'course-selector':CourseSelector,
         'bill-inputs':BillInputs
      },
      props: {
         user_id:{
            type: Number,
            default: 0
         },
      }, 
      data(){
         return {
             title:Helper.getIcon('courses')  + '  報名課程',

             courseSelector:{
                show:false,
                width:600,
                course_id:0
             },

             courseConfirmModal:{
                    show:false,
                    width:600,  
                    course:null
                },


             selectedCourses:[],
            

             form:{},

             payways:[],
         }
      },
      computed: {
         selectingCourse(){
            return Helper.isTrue(this.courseSelector.show)
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
         canSubmit(){
            if(!this.total) return false
            if(this.$refs.userview.isBusy()) return false
            return true
         }
      },
      watch:{
         
         total(){
            this.form.bill.total= this.total
            
         }
         
      },
      beforeMount() {
         this.fetchData()
      },
      methods: {
         init() {
				
         },
         fetchData(){
            let create=Signup.createByUser(this.user_id)
            create.then(data=>{
              
               this.form=new Form({
                  signups:data.signups,
                  bill:data.bill,
                  tuition:data.tuition,
                  
               })
			   this.payways=data.payways
					
               
            }).catch(error =>{
                Helper.BusEmitError(error)
            })
         },
         onCourseSelected(id){
            this.courseSelector.show=false
            this.courseSelector.course_id=id

           
            if(this.courseExist(id)) return false

            let getData=Signup.create(id,this.user_id)
            getData.then(data=>{
               let course=  data.course  
               
               if(course.error){
                  this.courseConfirmModal.msg=course.fullName +  '  ' +  course.error
                  this.courseConfirmModal.show=true

                  return false
               }

                course=new Course(data.course)

               this.selectedCourses.push(course)
              

               
               
            }).catch(error =>{
               Helper.BusEmitError(error)
            })

            // let getCourse=Course.show(id)
            // getCourse.then(data=>{
               
            //    let course=new Course(data.course)

            //    let canSignup=Helper.isTrue(course.canSignup)
            //    alert('canSignup')
            //    if(!canSignup){
            //       this.courseConfirmModal.msg=course.fullname +  ' 已停止報名'
            //       this.courseConfirmModal.show=true

            //       return false
            //    }

            //    this.selectedCourses.push(course)
              

               
               
            // }).catch(error =>{
            //    Helper.BusEmitError(error)
            // })
         },
         courseExist(id){
            if(this.selectedCourses.length < 1 ) return false
            let item=this.selectedCourses.find((course)=>{
               return course.id==id
            })

            if(item) return true
            return false
         },
         removeCourse(id){
            let index=this.selectedCourses.findIndex((course)=>{
               return course.id==id
            })

            this.selectedCourses.splice(index,1)
         },
         clearErrorMsg(name) {
            this.form.errors.clear(name)
         },
         onSubmit() {
            this.form.signups=[]
            this.selectedCourses.forEach((course)=> {
               this.form.signups.push({
                   user_id:this.user_id,
                   course_id:course.id,
                   tuition:course.tuition
               })
            })
               
            let store=Bill.store(this.form)
            store.then(data => {
                Helper.BusEmitOK()
                let url='/users/' + this.user_id
                Helper.redirect(url)
            })
            .catch(error => {
               Helper.BusEmitError(error)
            })
         },
         
      }
   }
</script>