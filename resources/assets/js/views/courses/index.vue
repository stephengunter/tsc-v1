<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="form-inline">
                
                <div class="form-group">
                    <select @change="onParamChanged"  v-model="params.term"    style="width:auto;" class="form-control selectWidth">
                        <option v-for="item in termOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select @change="onParamChanged"  v-model="params.center" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select @change="onParamChanged"  v-model="params.category" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in categoryOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select @change="onParamChanged" v-model="params.weekday" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in weekdayOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>

                <div v-if="groupReady"  class="form-group">&nbsp;&nbsp;群組課程
                    <select @change="onParentChanged" style="width:auto;"  v-model="params.parent"  class="form-control selectWidth" >
                         <option v-for="item in parentOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div v-if="hasParent"  class="form-group">
                   學分數：{{ parentCourse.credit_count }} &nbsp;
                   學分單價：{{ parentCourse.credit_price |  formatMoney }} &nbsp;
                   學費：{{ parentCourse.tuition |  formatMoney }}
                </div>
                

            </div>
        </div>
    </div>
     
    <course-list v-if="ready" :search_params="listSettings.params"  
        :hide_create="listSettings.hide_create" :version="version"  
        :can_select="listSettings.can_select"
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
                groupCategory:{},
                groupCourses:[],
                parentOptions:[],
                parentCourse:{},
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
                },

                listSettings:{
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
            isGroup(){
                if(!this.groupCategory) return false
                let category = parseInt(this.params.category)
                return category==parseInt(this.groupCategory.id)
            },
            hasParent(){
                if(!this.isGroup) return false
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

                    this.groupCategory=data.groupCategory

                    this.onParamChanged()

                    this.ready=true
                }).catch(error=>{
                    Helper.BusEmitError(error)
                    this.ready=false
                })
             
            },
            onParamChanged(parent){
                if(parent){
                    if(isNaN(parent))  this.params.parent=0
                    else  this.params.parent=Helper.tryParseInt(parent)
                }else{
                    this.params.parent=0
                }

                if(this.isGroup){
                    let center=this.params.center
                    let term=this.params.term
                    let params={ 
                        center:center,
                        term:term
                    }
                    let options=Course.groupOptions(params)
                    options.then(data => {
                        this.parentOptions=data.options
                        this.groupCourses=data.groupCourses
                        this.groupReady=true
                        this.setListParams()
                    })
                    .catch(error=>{
                           Helper.BusEmitError(error)                         
                    })   
                }else{
                    this.groupReady=false
                    this.setListParams()
                }
              
               
            },
            onParentChanged(){
                
                this.setListParams()
            },
            setListParams(){
                this.listSettings.params.term=this.params.term
                this.listSettings.params.center=this.params.center
                this.listSettings.params.category=this.params.category
                this.listSettings.params.weekday=this.params.weekday
                this.listSettings.params.parent=this.params.parent

                this.setParentCourse(this.listSettings.params.parent)
            },
            setParentCourse(id){
                if(Helper.tryParseInt(id) < 1) {
                   this.parentCourse={}
                }else{
                    for (let i = 0; i < this.groupCourses.length; i++) {
                        let course=this.groupCourses[i]
                        if(course.id==id){
                            this.parentCourse=course
                            break
                        }
                  
                    }
                }
                
                                   
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onGroupSelected(id){

                this.params.category=this.groupCategory.id
                this.onParamChanged(id)
                
            },
            onBeginCreate(){
                
                this.$emit('begin-create',this.parentCourse.id)
            },
            loadParentOptions(){
                let center=this.params.center
                let term=this.params.term
                let params={ 
                    center:center,
                    term:term
                }
                let options=Course.groupOptions(params)
                options.then(data => {
                    this.parentOptions=data.options
                })
                .catch(error=>{
                       Helper.BusEmitError(error) 
                    
                })   
            },
            
            
        },

    }
</script>