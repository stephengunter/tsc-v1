<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="form-inline">
                
                <combination-select :with_course="combinationSettings.withCourse"
                  @ready="onCombinationReady" @course-changed="setCourse">
                </combination-select>

            </div>
            
        </div>
     </div>
     
    <register-view v-if="ready" v-show="!student_id"
       :version="version" :course_id="course_id" 
       @loaded="onDataLoaded" @signup-selected="onSignupSelected"
       @student-selected="onStudentSelected" >
    </register-view>


    <student-view v-if="student_id" :id="student_id"
       @edit-user="onEditUser"
       @btn-back-clicked="onEndStudentView"
     >
       
    </student-view>

</div>

</template>

<script>
    import RegisterView from '../../components/register/view.vue'
    import StudentView from '../../components/student/view.vue'
    

    export default {
        name: 'RegisterIndex',       
        components: {
            'register-view':RegisterView,
            'student-view':StudentView
        },
        props: {
           
            hide_create:{
               type: Boolean,
               default: false
            }
        },
        data() {
            return {
                version:0,
                ready:false,
                course_id:0,

                course:null,
               
                can_edit:true,
                can_back:true,
                can_select:false,

                combinationSettings:{
                    withCourse:true,
                },

                student_id:0

             
            }
        },
        computed: {
            canCreate: function () {
                if(!this.course) return false
                return this.course.canCreateAdmit
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                
             
            },
            onCombinationReady(params){
                this.setCourse(params.course)
                this.ready=true
            },
            setCourse(val){
                this.course_id=val
            },
            onDataLoaded(course){
              
                this.course=course
            },
           
            onSignupSelected(signup_id){
                this.$emit('signup-selected',signup_id)
            },
            onEditUser(user_id){
                this.$emit('edit-user',user_id)
            },
            onBeginCreate(){
                this.$emit('begin-create',this.course_id)
            },
            onStudentSelected(id){
                this.student_id=id
            },
            onEndStudentView(){
                 this.student_id=0
                 this.version+=1
            }
            
            
        },

    }
</script>