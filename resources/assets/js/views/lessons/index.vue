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
     
    <lesson-list v-if="ready" :course_id="course_id" :hide_create="hideCreate" 
        :version="currenVersion" :can_select="can_select"
        @loaded="onLessonsLoaded"
        @selected="onSelected" @begin-create="onBeginCreate"
        @begin-initialize="onBeginInitialize" >
    </lesson-list>

    <modal :showbtn="false" :width="initializeSettings.width" :show.sync="initializeSettings.show"  @closed="initializeCanceled" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="initializeCanceled">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           
          </div>
        <div slot="modal-body" class="modal-body">
           
            <lesson-initialize v-if="initializeSettings.show" :course_id="course_id"
               @canceled="initializeCanceled"
               @success="onInitializeSuccess" @failed="onInitializeFailed">
                
            </lesson-initialize>

      
        </div>
    </modal>  
    
</div>

</template>

<script>
    import LessonList from '../../components/lesson/list.vue'
    import InitializeLessons from '../../components/lesson/initialize.vue'

    export default {
        name: 'LessonIndex',       
        components: {
            'lesson-list':LessonList,
            'lesson-initialize':InitializeLessons
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
                currenVersion:0,
                course_id:0,
               
                can_edit:true,
                can_back:true,
                can_select:false,

                combinationSettings:{
                    withCourse:true,
                },

                initializeSettings:{
                    course_id:0,
                    show:false,
                    width:1000,
                },

                canCreate:true



             
            }
        },
        computed:{
            hideCreate(){
                if(this.hide_create) return true

                return !this.canCreate
               
            },
            
        }, 
        watch:{
            version: function () {
               this.currenVersion+=1
            },
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                this.canCreate=!this.hide_create

                this.initializeSettings={
                    course_id:0,
                    show:false,
                    width:1000,
                }
             
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
            },
            onLessonsLoaded(data){
                this.canCreate = data.canCreate
            },
            onBeginInitialize(){
                 this.initializeSettings.show=true
            },
            initializeCanceled(){
                this.initializeSettings.show=false
            },
            onInitializeSuccess(){
                this.currenVersion+=1
                this.initializeSettings.show=false

                Helper.BusEmitOK('初始化成功')
                
            },
            onInitializeFailed(error){
               
                Helper.BusEmitError(error,'初始化失敗')
            }
            
            
        },

    }
</script>