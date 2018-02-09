<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="form-inline">
                
                <combination-select 
                  :with_course="combinationSettings.withCourse" :empty_course="combinationSettings.emptyCourse"
                  @ready="onCombinationReady" @term-changed="setTerm" @center-changed="setCenter"
                  @course-changed="setCourse">
               </combination-select>

            </div>
        </div>
     </div>
     
     <refund-list v-if="ready" :term_id="termId" :center_id="centerId" :course_id="courseId" 
       :version="version" :hide_create="hide_create" :can_select="can_select"
      @selected="onSelected" @begin-create="onBeginCreate">
     </refund-list>

</div>

</template>

<script>
    import RefundList from '../../components/refund/list.vue'
    

    export default {
        name: 'RefundIndex',       
        components: {
            'refund-list':RefundList
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
                
                termId:0,
                centerId:0,
                courseId:0,
               
                can_edit:true,
                can_back:true,
                can_select:false,

                combinationSettings:{
                    withCourse:true,
                    emptyCourse:true
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
                this.setTerm(params.term)
                this.setCenter(params.center)
                this.setCourse(params.course)

                this.ready=true
            },
           
            setCourse(val){
                this.courseId=val
            },
            setTerm(val){
                 this.termId=val
            },
            setCenter(val){
                this.centerId=val
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