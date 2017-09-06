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
     
    <signup-list v-if="ready" :course_id="course_id" :hide_create="hide_create" 
        :version="version" :can_select="can_select" :for_refund="for_refund"
        @selected="onSelected" @begin-create="onBeginCreate">
    </signup-list>

</div>

</template>

<script>
    import SignupList from '../../components/signup/list.vue'
    

    export default {
        name: 'SignupIndex',       
        components: {
            'signup-list':SignupList
        },
        props: {
            version: {
              type: Number,
              default: 0
            },
            hide_create:{
               type: Boolean,
               default: false
            },
            for_refund:{
               type: Boolean,
               default: false
            },
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
            
            onCombinationReady(course){
                this.setCourse(course)
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