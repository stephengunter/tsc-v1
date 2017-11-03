<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="form-inline">
                
                <div class="form-group">
                    <select @change="onParamChanged"  v-model="params.term"    style="width:auto;" class="form-control selectWidth">
                        <option v-for="(item,index) in termOptions" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select @change="onParamChanged"  v-model="params.center" style="width:auto;" class="form-control selectWidth">
                         <option v-for="(item,index) in centerOptions" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select @change="onParamChanged"  v-model="params.category" style="width:auto;" class="form-control selectWidth">
                         <option v-for="(item,index)  in categoryOptions" :key="index"  :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select @change="onParamChanged" v-model="params.weekday" style="width:auto;" class="form-control selectWidth">
                         <option v-for="(item,index) in weekdayOptions" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>

                <div v-if="hasParent" class="form-group">&nbsp;&nbsp;群組課程：
                    <span>
                    &nbsp; {{ parentCourse.name  }}
                        <button type="button" @click.prevent="clearParent" class="close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </span>
                    
       
                </div>
                
                

            </div>
        </div>
    </div>
     
    <course-list v-if="ready" :no_page="listSettings.no_page" 
        :can_edit_number="listSettings.canEditNumber"  :search_params="listSettings.params"  
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

                groupReady:false,

                termOptions:[],
                categoryOptions:[],
                centerOptions:[],
                weekdayOptions:[],
               
                params:{
                    term:0,
                    center:0,
                    category:0,
                    weekday:0,
                    parent:0,
                },

                listSettings:{
                    no_page:true,
                    canEditNumber:true,
                    can_select:false,
                    hide_create:false,
                    params:{
                        term:0,
                        center:0,
                        category:0,
                        weekday:0,
                        parent:0
                    },
                }
                
             
            }
        },
        beforeMount() {
             this.init()
        },
        computed:{
            hasParent(){
               
                if(!this.parentCourse) return false
                  
                return  Helper.tryParseInt(this.parentCourse.id) > 0
            }
        }, 
        methods: {
            init(){
                this.listSettings.hide_create=this.hide_create
                let options=Course.indexOptions()
                options.then(data=>{
                    this.termOptions=data.termOptions
                    this.params.term=this.termOptions[0].value

                    this.centerOptions=data.centerOptions
                    let allCenters={ text:'全部開課中心' , value:'0' }
                    this.centerOptions.splice(0, 0, allCenters);
                    this.params.center=this.centerOptions[0].value

                    this.categoryOptions=data.categoryOptions
                    let defaultItem={ text:'全部課程分類' , value:'0' }
                    this.categoryOptions.splice(0, 0, defaultItem);
                    this.params.category=this.categoryOptions[0].value

                    this.weekdayOptions=data.weekdayOptions
                    let allWeekdays={ text:'不限星期' , value:'0' }
                    this.weekdayOptions.splice(0, 0, allWeekdays);
                    this.params.weekday=this.weekdayOptions[0].value

                   

                    this.onParamChanged()

                    this.ready=true
                }).catch(error=>{
                    Helper.BusEmitError(error)
                    this.ready=false
                })
             
            },
            onCoursesLoaded(data){
               
               this.parentCourse = data.parentCourse
               
            },
            onParamChanged(){
                this.setListParams()
               
            },
            setListParams(){
                this.listSettings.params.term=this.params.term
                this.listSettings.params.center=this.params.center
                this.listSettings.params.category=this.params.category
                this.listSettings.params.weekday=this.params.weekday
                this.listSettings.params.parent=this.params.parent

                
            },
            clearParent(){
                this.params.parent=0
                this.parentCourse=null
                this.onParamChanged()
            },
            OnDetails(id){
                this.$emit('details',id)
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onGroupSelected(id){
                this.params.parent=id
                this.onParamChanged()
                
            },
            onBeginCreate(){
                let parent=0
                if(this.parentCourse) parent= this.parentCourse.id
                this.$emit('begin-create',parent)
            },
            
            
            
        },

    }
</script>

