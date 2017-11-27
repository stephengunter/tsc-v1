<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div  class="panel-title">
                <options-filter :params="params" :parent_course="parentCourse"
                    :options="options"
                    @ready="onFilterReady"  @param-changed="onParamChanged"
                    @clear-parent="clearParent">
                </options-filter>
            </div>
            <div>
                
                <button :disabled="!canSubmit" @click.prevent="submit" class="btn btn-success" >
                    <slot  name="btn">
                        確認送出
                    </slot>   
                </button>
               
                
            </div>
        </div>
    </div>
    <course-list v-if="ready" :source_url="listSettings.source_url" :no_page="listSettings.no_page" 
        :show_title="listSettings.show_title" :title_text="listSettings.title_text"
        :no_search="listSettings.no_search"
        :can_edit_number="listSettings.canEditNumber"  :search_params="params"  
        :hide_create="listSettings.hide_create" :version="listSettings.version"  
        :multi_select="listSettings.multi_select" :selected_ids="checked_ids"
        @data-loaded="onCoursesLoaded"
        @details="onDetails"
        @checked="onChecked" @unchecked="onUnChecked"
        @group-selected="onGroupSelected" 
        @checkall="checkAll"   @uncheckall="unCheckAll" > 
    </course-list> 
</div>    
</template>



<script>
    import CourseList from './list.vue'
    export default {
        name: 'CourseSelector',
        components: {
            'course-list':CourseList                
        },
        props: {
            source_url:{
                type: String,
                default: ''
            },
            params: {
                type: Object,
                default: null
            },
            options: {
                type: Object,
                default: null
            },
            title_text:{
                type: String,
                default: ''
            }
            
        },
        beforeMount() {
            this.ready=false
            
            this.init()
        },
        computed:{
            canSubmit() {
                return  this.checked_ids.length > 0
            }
        }, 
        data() {
            return {
                
                ready:false,
                parentCourse:null,             
                
                courseList:[],

                listSettings:{
                    version:0,
                    source_url:'',
                    show_title:true,
                    title_text:this.title_text,
                    no_search:true,
                    no_page:true,
                    canEditNumber:false,
                    multi_select:true,
                    hide_create:true,
                    
                },

                checked_ids:[],
            }
        },
        methods: {
            init(){
                if(this.source_url){
                    this.listSettings.source_url=this.source_url
                }else{
                    this.listSettings.source_url=Course.source()
                } 

                this.checked_ids=[]
                this.courseList=[]
            },
            onFilterReady(params){
                for(let property in params){
                    this.params[property]=params[property]
                }

                this.ready=true
                
            },
            refresh(){
                this.checked_ids=[]
                this.courseList=[]
                this.listSettings.version+=1
            },
            onCoursesLoaded(data){
                this.courseList=data.model.data
                this.parentCourse = data.parentCourse
               
            },
            onDetails(id){
                this.$emit('details',id);                
            },
            onGroupSelected(id){
                this.params.parent=id  
            },
            clearParent(params){
                this.parentCourse=null
                this.onParamChanged(params)
            },
            onParamChanged(params){
                for(let property in params){
                    this.params[property]=params[property]
                } 
                this.unCheckAll()
            },
            beenChecked(id){
                return this.checked_ids.includes(id)
            },
            onChecked(id){
                
                if(!this.beenChecked(id))  this.checked_ids.push(id) 
            },
            onUnChecked(id){
                 
                let index= this.checked_ids.indexOf(id)
                if(index >= 0)  this.checked_ids.splice(index, 1) 
                
            },
            checkAll(){
                if(!this.courseList)  return false
                this.courseList.forEach( course => {
                     this.onChecked(course.id)
                })
            },
            unCheckAll(){
                
                this.checked_ids=[]
            },
            submit(){
                this.$emit('submit',this.checked_ids);               
            }
        }
     }
</script>