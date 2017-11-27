<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
               
            <combination-select :search_params="search_params"
                @ready="onCombinationReady" >
            </combination-select>

            <button @click="viewSub"  v-show="groupAndParent" type="button" class="btn-sm btn btn-warning">
               <span aria-hidden="true" class="glyphicon glyphicon-search"></span>
               學分
            </button>
        </div>
     </div>
     
    <signup-list v-if="ready" :course_id="course_id" :hide_create="hide_create" 
        :version="version" :can_select="can_select" :for_refund="for_refund"
        @loaded="onSignupListLoaded"
        @selected="onSelected" @begin-create="onBeginCreate">
    </signup-list>

    <modal :showbtn="false" :width="subSettings.width" :show.sync="subSettings.show"  @closed="closeSub" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="closeSub">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           
          </div>
        <div slot="modal-body" class="modal-body">
           
            <sub-course-list  :courses="subCourses">
            </sub-course-list>

      
        </div>
    </modal> 

</div>

</template>

<script>
    import SignupList from '../../components/signup/list.vue'
    import SubCourseList from '../../components/course/sub/list.vue'


    export default {
        name: 'SignupIndex',       
        components: {
            'signup-list':SignupList,
            'sub-course-list':SubCourseList
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

                search_params:{
                    term:0,
                    center:0,
                    parent:0,
                    sub:0,
                    reviewed:1
                },
                

                groupAndParent:false,
                subSettings:{
                    width:800,
                    show:false
                },

                subCourses:[]

             
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
            onSignupListLoaded(data){
               this.groupAndParent= data.groupAndParent
            },
            viewSub(){
                let getData=Course.subCourses(this.course_id)
                
                getData.then(data=>{
                    this.subCourses=data.courseList
                    this.subSettings.show=true
                }).catch(error=>{
                    Helper.BusEmitError(error)  
                 
                }) 

            },
            closeSub(){
                this.subSettings.show=false
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