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
     
    <register-view v-if="ready" :course_id="course_id" 
       @loaded="onDataLoaded" @signup-selected="onSignupSelected">
    </register-view>

</div>

</template>

<script>
    import RegisterView from '../../components/register/view.vue'
    

    export default {
        name: 'RegisterIndex',       
        components: {
            'register-view':RegisterView
        },
        props: {
            version: {
              type: Number,
              default: 0
            },
            hide_create:{
               type: Boolean,
               default: false
            }
        },
        data() {
            return {
                ready:false,
                course_id:0,

                course:null,
               
                can_edit:true,
                can_back:true,
                can_select:false,

                combinationSettings:{
                    withCourse:true,
                }

             
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
            onBeginCreate(){
                this.$emit('begin-create',this.course_id)
            }
            
            
        },

    }
</script>