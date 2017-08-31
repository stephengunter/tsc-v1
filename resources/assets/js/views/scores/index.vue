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
     
    <score-list v-if="ready" 
       :version="version" :course_id="course_id" 
       @loaded="onDataLoaded" 
        >
    </score-list>

    <modal :showbtn="false" :width="importingSettings.width" :show.sync="importingSettings.show"  @closed="importingCanceled" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="importingCanceled">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           
          </div>
        <div slot="modal-body" class="modal-body">
           
            <score-importing v-if="importingSettings.show" :course_id="course_id"
               
               @success="onImportSuccess" @failed="onImportFailed">
                
            </score-importing>

      
        </div>
    </modal>  


</div>

</template>

<script>
    import ScoreList from '../../components/score/list.vue'
    import ImportScores from '../../components/score/import.vue'

    export default {
        name: 'ScoreIndex',       
        components: {
           'score-list':ScoreList,
           'score-importing':ImportScores,
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

                listSettings:{

                },

                importingSettings:{
                    course_id:0,
                    show:false,
                    width:1000,
                },

                student_id:0

             
            }
        },
        computed: {
            
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
            importingCanceled(){

            },
            onImportSuccess(){

            },
            onImportFailed(){

            }

            
            
        },

    }
</script>