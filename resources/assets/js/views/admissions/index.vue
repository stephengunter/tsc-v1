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
     
    <admission-view v-if="ready" :course_id="course_id" >
       
       
    </admission-view>

</div>

</template>

<script>
    import AdmissionView from '../../components/admission/view.vue'
    

    export default {
        name: 'SignupIndex',       
        components: {
            'admission-view':AdmissionView
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
               
                can_edit:true,
                can_back:true,
                can_select:false,

                combinationSettings:{
                    withCourse:true,
                }

             
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
            onSelected(id){
                this.$emit('selected',id)
            },
            onBeginCreate(){
                this.$emit('begin-create',this.course_id)
            }
            
            
        },

    }
</script>