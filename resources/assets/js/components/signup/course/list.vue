<template>
   <div class="panel panel-default">
      <div class="panel-heading">           
         <span class="panel-title">
            <h4 v-html="title"></h4> 
         </span>  
         <button v-show="can_add" @click.prevent="onAddCourseClicked" class="btn btn-primary btn-sm" >
            <span class="glyphicon glyphicon-plus"></span> 新增
         </button>         
      </div>
      <div class="panel-body">
         
         <div class="row">
            <div  class="col-sm-12"> 
              
               <table class="table table-striped">
                  <thead>
                     <tr>
								<th>課程編號</th>
								<th>課程名稱</th>
								<th>上課時間</th>
								<th>課程期間</th>
								<th>課程費用</th>
								<th v-if="can_remove"></th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(course,index) in courses" :key="index" >
								<td>{{ course.number }}</td>
								<td>{{ course.fullName }}</td>
								<td v-html="course.classTimesText"></td>
								<td>{{ course.period }}</td>
								<td>{{ course.tuition | formatMoney}}</td>
								<td>
									<button @click.prevent="removeCourse(course.id)" v-if="can_remove" class="btn btn-danger btn-xs">
										<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
									</button>
								</td>
                     </tr>
                  </tbody>
               </table>
               <h3 v-show="total>0">合計：{{ total | formatMoney }}元</h3> 
            </div>
         </div> 
      </div>     <!-- panel-body   -->
		
		<course-selector :show="selectorSettings.show" :params="selectorSettings.params"
       :disable_terms="coursesCount>0" :disable_centers="coursesCount>0"
       @selected="onCourseSelected" @cancel="selectorSettings.show=false">

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
import CourseSelector from './selector.vue'
export default {
	name:'SignupCourseList',
	components: {
		'course-selector':CourseSelector,
	},
   props: {
      init_courses:{
         type: Array,
         default: null
      },
      can_remove:{
         type: Boolean,
         default: false
      },
		can_add:{
         type: Boolean,
         default: false
      },
   }, 
   data() {
      return {
			title:Helper.getIcon('courses')  + '  報名課程',
			courses:[],

			selectorSettings:{
				show:false,
				width:600,
				course_id:0,

				params:{
					term:0,
					center:0
				},

				disable_terms:false,
				disable_centers:false,
			},

			courseConfirmModal:{
				show:false,
				width:600,  
				course:null
			},
            
         
      }
	},
	computed: {
		selectingCourse(){
			return Helper.isTrue(this.courseSelector.show)
		},
		coursesCount(){
			if(!this.courses) return 0;
			return this.courses.length
		},
		total(){
			if(!this.coursesCount) return 0

			let tuitions = this.courses.map((course) => {
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
	beforeMount() {
      this.init()
   },
   methods: {
		init(){
			if(this.init_courses){
				this.courses=this.init_courses.slice(0);
			}
		},
		onCourseSelected(id){
			this.selectorSettings.show=false
			
			if(this.courseExist(id)) return false

			let getData=Signup.create(id,this.user_id)
			getData.then(data=>{
				let course=  data.course  
				
				if(course.error){
					
					this.onAddCourseError(course)
					return false
				}
				course=new Course(data.course)

				this.courses.push(course)

				this.$emit('course-changed')
				
				
			}).catch(error =>{
				Helper.BusEmitError(error)
			})
		},
		courseExist(id){
			if(!this.coursesCount ) return false
		
			let index=this.courses.findIndex((item)=>{
				return item.id==id
			})
			return index>=0
		},
		onAddCourseError(course){
			if(course.error){
				this.courseConfirmModal.msg=course.fullName +  '  ' +  course.error
				this.courseConfirmModal.show=true
				
			}
		},
      onAddCourseClicked(){
			if(this.coursesCount){
				let course=this.courses[0]
				this.showCourseSelector(course)
			}else{
				this.showCourseSelector()
			}

         
		},
		showCourseSelector(course=null){
			if(course){
				this.selectorSettings.params.term=course.term_id
				this.selectorSettings.params.center=course.center_id

				this.selectorSettings.disable_terms=true
				this.selectorSettings.disable_centers=true
			}else{
				this.selectorSettings.params.term=0
				this.selectorSettings.params.center=0

				this.selectorSettings.disable_terms=false
				this.selectorSettings.disable_centers=false
			}


			this.selectorSettings.show=true
		},
		getCourses(){
			return this.courses
		},
		getTotal(){
			return this.total
		},
      removeCourse(id){
			
			
			let index=this.courses.findIndex((item)=>{
				return item.id==id
			})

			if(index>=0){
				this.courses.splice(index, 1)
			} 

			this.$emit('course-changed')
      }

   }
}
</script>

