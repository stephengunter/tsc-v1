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
               
              
                can_select:false,
             
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

                    this.ready=true
                }).catch(error=>{
                    Helper.BusEmitError(error)
                    this.ready=false
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