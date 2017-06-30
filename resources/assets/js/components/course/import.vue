<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="form-inline">
                
                <div class="form-group">
                    <select  v-model="params.term"    style="width:auto;" class="form-control selectWidth">
                        <option v-for="item in termOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select  v-model="params.center" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select  v-model="params.category" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in categoryOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select  v-model="params.weekday" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in weekdayOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
            </div>
        </div>
    </div>
     
    <course-list v-if="ready" :search_params="params"  :hide_create="hide_create" :version="version"  
        :can_select="can_select"
        @selected="onSelected" @begin-create="onBeginCreate">
    </course-list>

</div>

</template>

<script>
    import CourseSelector from '../../components/course/selector.vue'

    export default {
        name: 'CourseImport',       
        components: {
            'course-selector':CourseSelector
        },
        props: {
           
        },
        data() {
            return {
                ready:false,

                dataviewer:{},
                source: Course.source(),
                defaultSearch:'name',
                defaultOrder:'begin_date',  
                direction:'desc', 
                perPage:99,
                
                
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
               

             
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                let options=Course.indexOptions()
                options.then(data=>{
                    this.termOptions=data.termOptions
                    this.params.term=this.termOptions[0].value

                    this.centerOptions=data.centerOptions
                    this.params.center=this.centerOptions[0].value

                    this.categoryOptions=data.categoryOptions
                    let defaultItem={ text:'全部課程分類' , value:'0' }
                    this.categoryOptions.splice(0, 0, defaultItem);
                    this.params.category=this.categoryOptions[0].value

                    this.weekdayOptions=data.weekdayOptions
                    let allWeekdays={ text:'不限星期' , value:'0' }
                    this.weekdayOptions.splice(0, 0, allWeekdays);
                    this.params.weekday=this.weekdayOptions[0].value
                    
                    this.dataviewer=new DataViewerService(this.source, this.defaultOrder, 
                                  this.direction , this.defaultSearch, this.perPage, this.params)
                    
                }).catch(error=>{
                    Helper.BusEmitError(error)
                    this.ready=false
                })
             
            },
            fetchData(){
                
                axios.get(url)
                .then(response => {
                    this.courseList=data.courseList
                })
                .catch(error=> {
                     Helper.BusEmitError(error)
                })  
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