<template>
<div >
    <div class="panel panel-default" >
        <div class="panel-heading">
            <options-filter :params="params" :parent_course="parentCourse"
              @ready="onFilterReady"  @param-changed="onParamChanged"
              @clear-parent="clearParent">
            </options-filter>
        </div> <!--  End Head  -->
    </div>
     
    <course-list v-if="ready" :no_page="listSettings.no_page" 
        :can_edit_number="listSettings.canEditNumber"  :search_params="params"  
        :hide_create="hide_create" :version="version"  
        :can_select="listSettings.can_select"
        @details="OnDetails"
        @data-loaded="onCoursesLoaded"
        @selected="onSelected" @begin-create="onBeginCreate"
        @group-selected="onGroupSelected">
    </course-list>

</div>

</template>

<script>
    import CourseList from '../../components/course/list.vue'
    

    export default {
        name: 'CourseIndex',       
        components: {
            'course-list':CourseList
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
                parentCourse:null,

                //groupReady:false,

                params:{
                    term:0,
                    center:0,
                    category:0,
                    weekday:0,
                    parent:0
                },
                

                listSettings:{
                    no_page:true,
                    canEditNumber:true,
                    can_select:false,
                    hide_create:false,
                }
                
             
            }
        },
        beforeMount() {
             this.init()
        },
        computed:{
            
        }, 
        methods: {
            init(){
                this.listSettings.hide_create=this.hide_create
            },
            onFilterReady(params){
                for(let property in params){
                    this.params[property]=params[property]
                }

                this.ready=true
                
            },
            onCoursesLoaded(data){
               this.parentCourse = data.parentCourse
            },
            onParamChanged(params){
                for(let property in params){
                    this.params[property]=params[property]
                } 
               
            },
            clearParent(params){
                this.parentCourse=null
                this.onParamChanged(params)
            },
            OnDetails(id){
                this.$emit('details',id)
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onGroupSelected(id){
                this.params.parent=id  
            },
            onBeginCreate(){
                let parent=0
                if(this.parentCourse) parent= this.parentCourse.id
                this.$emit('begin-create',parent)
            },
            
            
            
        },

    }
</script>

