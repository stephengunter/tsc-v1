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
                <li>
                    <a @click="activeIndex=1" href="#classtime" data-toggle="tab">上課時間</a>
                </li>
                <li>
                     <a @click="activeIndex=2" href="#schedule" data-toggle="tab">預定進度</a>
                </li>
                <li>
                     <a @click="activeIndex=3" href="#signupRecord" data-toggle="tab">報名紀錄</a>
                </li>
                <li>
                     <a @click="activeIndex=4" href="#lesson" data-toggle="tab">課堂紀錄表</a>
                </li>
                
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
                <div class="tab-pane fade" id="signupRecord">
                    <div  v-if="activeIndex==3"  >
                       <signup-list v-show="!signupRecordSettings.creating" :course_id="id" 
                            :hide_create="signupRecordSettings.hide_create" 
                            :version="signupRecordSettings.version" 
                            :can_select="signupRecordSettings.can_select"
                            @selected="onSignupRecordSelected" @begin-create="onBeginCreateSignupRecord">
                       </signup-list>
                       <create-signup v-show="signupRecordSettings.creating" :course_id="id" 
                            @canceled="onCreateSignupCanceled" @saved="onSignupCreated">
                       </create-signup>
                     </div>  
                </div>
                <div class="tab-pane fade" id="lesson">
                      <div  v-if="activeIndex==4" >
                         <lesson-list v-show="!lessonSettings.creating" :course_id="id" 
                            :hide_create="lessonSettings.hide_create" 
                            :version="lessonSettings.version" 
                            :can_select="lessonSettings.can_select"
                            @selected="onLessonSelected" @begin-create="onBeginCreateLesson">
                         </lesson-list>

                      </div>
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
    import SignupList from '../../components/signup/list.vue'
    import CreateSignup from '../../components/signup/create.vue'
    import LessonList from '../../components/lesson/list.vue'

    
    export default {
        name: 'CourseDetails',
        components: {
           'course' : CourseComponent,
           'signup-info' : SignupInfoComponent,
           'classtime' : ClasstimeComponent,
           'schedule' : ScheduleComponent,
           'signup-list':SignupList,
           'create-signup':CreateSignup,
           'lesson-list':LessonList,
         
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
               },
  
               signupRecordSettings:{
                   hide_create:false,
                   version:0,
                   can_select:false,
                   creating:false

               },
               lessonSettings:{
                   hide_create:false,
                   version:0,
                   can_select:false,
                   creating:false

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
                 
            },
            onSignupRecordSelected(id){
               this.$emit('signup-selected', id)
            },
            onBeginCreateSignupRecord(){
                this.signupRecordSettings.creating=true
            },
            onCreateSignupCanceled(){
                this.signupRecordSettings.creating=false
                this.signupRecordSettings.version +=1
            },
            onSignupCreated(){
                this.signupRecordSettings.creating=false
                this.signupRecordSettings.version +=1
            },
            onLessonSelected(id){
               this.$emit('lesson-selected', id)
            },
            onBeginCreateLesson(){
                this.lessonSettings.creating=true
            },
             
            
        }, 

    }
</script>