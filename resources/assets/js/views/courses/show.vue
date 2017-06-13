<template>
    <div>
        <course :id="id" :version="version" 
        @courseLoaded="onCourseLoaded" @deleted="backToIndex">
            
        </course>

        <div v-if="courseLoaded" id="tabCourse" class="panel with-nav-tabs panel-default">
            <div class="panel-heading">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a @click="activeIndex=0" href="#signupInfo" data-toggle="tab">報名資訊</a>
                        </li>
                        <li class="">
                            <a @click="activeIndex=1" href="#signupRecord" data-toggle="tab">報名紀錄</a>
                        </li>
                        <li class="">
                            <a @click="activeIndex=2" href="#classtime" data-toggle="tab">上課時間</a>
                        </li>
                        <li class="">
                            <a @click="activeIndex=3" href="#schedule" data-toggle="tab">預定進度</a>
                        </li>
                        <li class="">
                            <a @click="activeIndex=4" href="#lesson" data-toggle="tab">課堂紀錄表</a>
                        </li>
                        
                    </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="signupInfo">
                        <signup-info v-if="activeIndex==0" :id="id" @signupSaved="onSignupSaved" 
                        :canEdit="course.canEdit"></signup-info>  
                    </div>
                    <div class="tab-pane fade" id="signupRecord">
                          <signup v-if="activeIndex==1" :course_id="id" 
                            :canEdit="course.canEdit"></signup>  
                    </div>
                    <div class="tab-pane fade" id="classtime">
                         <classtime v-if="activeIndex==2" :course_id="id" 
                         :canEdit="course.canEdit" @classTimeChanged="onClassTimeChanged"></classtime>
                    </div>
                    <div class="tab-pane fade" id="schedule">
                        <schedule v-if="activeIndex==3" :course_id="id" :canEdit="course.canEdit"></schedule>
                    </div> 
                     <div class="tab-pane fade" id="lesson">
                        <lesson v-if="activeIndex==4" :course_id="id" :canEdit="course.canEdit"></lesson>
                    </div>                        
                </div>
            </div>
        </div>   <!-- end tabCourse -->
       


    </div>
</template>


<script>
    import Course from '../../components/course/course.vue'

    import SignupInfo from '../../components/course/signup.vue'
    import Signup from '../../components/signup/signup.vue'
    import Classtime from '../../components/classtime/classtime.vue'
    import Schedule from '../../components/schedule/schedule.vue'
    import Lesson from '../../components/lesson/lesson.vue'
     

    export default {
        components:{
           Course,
           'signup-info':SignupInfo,
           Signup,
           Schedule,
          'classtime':Classtime,
           Lesson
        },
        name:'CourseView',
        data()  {
            return {                
                id:0,
                
                course:{
                    canEdit:false
                },
                courseLoaded:false,
                activeIndex:0,
                version:0
            }
        },
        beforeMount(){
           this.init()
        },
        watch: {
            '$route': 'init'
        },
        methods:{
            init(){
                this.id=this.$route.params.id
              
                this.activeIndex=0
                this.course={
                     canEdit:false
                }
                this.courseLoaded=false

                this.version=0
            },
            onCourseLoaded(course){
                 this.course=course
                 this.courseLoaded=true
            },
            onCourseSaved(course){  
                this.course=course
                this.isReadOnly=true
            },   
            onSignupSaved(){
                this.version +=1
            },
            onClassTimeChanged(){
                 this.version +=1
            },
             backToIndex() {
                this.$router.push('/courses')
            },

        }

    }
</script>