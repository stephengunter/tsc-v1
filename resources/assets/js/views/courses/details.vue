<template>
<div>
  <course v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="onCourseUpdated" @loaded="onDataLoaded" :version="version"
     @btn-back-clicked="onBtnBackClicked" @deleted="onCourseDeleted" > 

  </course>

  <div v-if="loaded" id="tabCourse" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a @click="activeIndex=0" href="#signupInfo" data-toggle="tab">報名資訊</a>
                </li>
                <li class="">
                    <a @click="activeIndex=1" href="#classtime" data-toggle="tab">上課時間</a>
                </li>
                <li class="">
                     <a @click="activeIndex=2" href="#schedule" data-toggle="tab">預定進度</a>
                </li>
                <!-- <li>
                     <a @click="activeIndex=1" href="#contactinfo" data-toggle="tab">聯絡資訊</a>
                </li>
                <li>
                     <a @click="activeIndex=2" href="#centers" data-toggle="tab">所屬中心</a>
                </li> -->
                
            </ul>
        </div>
        <div  class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="signupInfo">
                    <signup-info v-if="activeIndex==0" :course="course" 
                       @saved="onSignupInfoSaved"  >
                    </signup-info>  
                </div>
                <div class="tab-pane fade" id="classtime">
                    <classtime v-if="activeIndex==1"  
                      :course_id="id"   :canEdit="course.canEdit" 
                     @created="onClasstimeChanged" @deleted="onClasstimeChanged"
                     @updated="onClasstimeChanged"   >             
                    </classtime>
                </div>
                <div class="tab-pane fade" id="schedule">
                    <schedule v-if="activeIndex==2"  
                      :course_id="id"   :canEdit="course.canEdit" 
                     @created="onScheduleChanged" @deleted="onScheduleChanged"
                     @updated="onScheduleChanged"   >             
                    </schedule>
                </div>                 
            </div>
        </div>
  </div>



  


</div>
</template>

<script>
    import CourseComponent from '../../components/course/course.vue'
    import SignupInfoComponent from '../../components/course/signupinfo/view.vue'
    import ClasstimeComponent from '../../components/classtime/classtime.vue'
    import ScheduleComponent from '../../components/schedule/schedule.vue'
    
    
    
    export default {
        name: 'CourseDetails',
        components: {
           'course' : CourseComponent,
           'signup-info' : SignupInfoComponent,
           'classtime' : ClasstimeComponent,
           'schedule' : ScheduleComponent
         
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            can_back:{
               type: Boolean,
               default: true
            },
        },
        data() {
            return {
               loaded:false,
              
               readonly:true,
               course:null,
               version:0,

               activeIndex:0,
               user:{
                  contact_info:0,
               },

               userSettings:{
                    can_edit:true,
                    can_back:false,
                    hide_delete:true,
                    role:this.role
               },
               contactInfoSettings:{
                    id:0,
                    user_id:0,
                    can_edit:true,
                    can_back:false,
                    show_residence:true,
               },

               backTuitionSettings:{
                  hide_create:false
               }
            }
        },
        computed:{
          
        },
        beforeMount(){
           this.init()
        },
        methods: {
            init(){
              this.loaded=false
              this.readonly=true
              this.activeIndex=0

              this.course=null
              this.user={
                  contact_info:0,
               }


            },
            toBoolean(val){
               return val=='true'
            },
            onDataLoaded(course){
                this.loaded=true
                this.course=course
            },
            btnEditClicked(){    
              this.beginEdit()
            },
            beginEdit(){
               this.readonly=false
            },
            editCanceled(){
               this.readonly=true
            },
            onSignupInfoSaved(signupinfo){
                this.version += 1
            },
            onCourseUpdated(){
               this.init()
            },
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onCourseDeleted(){
               this.$emit('course-deleted')
            },
            onClasstimeChanged(){
                 this.version += 1
            },
            onScheduleChanged(){
                 
            }
            
        }, 

    }
</script>