<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span  class="panel-title">
                <span  class="panel-title">
                    <h4><i class="fa fa-calendar-o" aria-hidden="true"></i> 課程預定進度</h4>                  
                </span>
            </span>
            <div class="form-inline">   
                <select v-show="teacherOptions.length" v-model="form.import.teacher_id"  @change="loadCourseOptions" class="form-control" >
                    <option v-for="(item,index) in teacherOptions" :key="index" :value="item.value" v-text="item.text"></option>
                </select>
                 &nbsp;&nbsp;
                <select v-show="courseOptions.length" v-model="selectedCourse"  class="form-control" >
                    <option v-for="(item,index)  in courseOptions" :key="index" :value="item.value" v-text="item.text"></option>
                </select>
            </div>
            <div>
                <button  v-show="isValid" @click.prevent="courseSelected" class="btn btn-success btn-sm" >確認送出</button>
                 &nbsp;&nbsp;
                <button  class="btn btn-default btn-sm" @click="changeMode">
                    <span v-if="!showFilter" class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    <span v-if="showFilter" class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                </button>
            </div>
        </div>
        <div class="panel-body">
            <div class="filter" v-if="showFilter">
                
                <div class="filter-input">
                    <input type="text" class="form-control"  v-model="filterText" @keyup.enter="doSearch" placeholder="輸入課程名稱搜尋">
                      
                </div>
                
                <div  class="filter-btn">
                    <button @click.prevent="doSearch" class="btn btn-primary btn-sm btn-block">搜尋</button>
                </div>
            </div>
            <table v-show="hasData" class="table table-striped" style="width: 95%;">
             <thead> 
                <tr> 
                    <th style="width:10%">#</th> 
                    <th style="width:35%">課目標題</th> 
                    <th style="width:35%">內容</th> 
                    <th style="width:20%">材料</th>
                   
                </tr> 
            </thead>
            <tbody> 
               
                <tr v-for="(schedule,index) in scheduleList" :key="index"> 
                    <th scope="row" v-text="schedule.order"></th> 
                    <td v-text="schedule.title"></td> 
                    <td v-text="schedule.content"></td>
                    <td v-text="schedule.materials"></td>                     
                </tr> 
                
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->
       
    </div>
</template>



<script>
     export default {
        name: 'ScheduleImportor', 
        props: {
            course_id: {
                type: Number,
                default: 0
            },  
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.scheduleList.length) return true
                return false    
            },
            isValid(){
                if(Helper.tryParseInt(this.selectedCourse) <1 ) return false
                return this.hasData
            }
        },
        data() {
            return {
                
                loaded:false,  
                courseOptions:[],             
                scheduleList:[],    

                selectedCourse:0,            
            
                form:{},
                showFilter:false,

                filterText:'',
             
            }
        },
        watch: {
            selectedCourse: function () {
               this.fetchData()
            }
           
        },
        methods: {
            init(){
               
                this.loaded=false  
                
                this.selectedCourse=0

                this.teacherOptions=[]
                this.courseOptions=[]   

                this.scheduleList=[]       

                this.form=new Form({
                    import:{}
                })         
                

                let create=ScheduleImport.create(this.course_id)  //this.create()

                create.then((data) => {
                    this.form=new Form({
                        import:data.import
                    })
                    this.teacherOptions = data.teacherOptions 
                    
                    this.setCourseOptions(data.courseOptions)
                }).catch(error => {
                    Helper.BusEmitError(error)
                    this.loaded = false
                })
            },
            fetchData(){
                let course=this.selectedCourse
                if(!course) return false
                let getData=Schedule.index(course)               
              
                getData.then(data => {

                   this.scheduleList=data.scheduleList
                   this.loaded = true
                    
                })
                .catch(error => {
                    Helper.BusEmitError(error)
                    this.loaded = false
                })
            },
            
            loadCourseOptions(){
                let teacher=this.form.import.teacher_id
                let params={
                    teacher:teacher
                }
                let options=Course.options(params)
                options.then((data) => {
                     this.setCourseOptions(data.options)
                }).catch(error => {
                    Helper.BusEmitError(error)
                })
            },
            setCourseOptions(options){

                for(let i = options.length-1; i>=0; i--){

                   if (options[i].value == this.course_id ) {
                  
                          options.splice(i, 1);
                   }                           
                }
                if(options.length){
                    this.selectedCourse=options[0].value
                }
                this.courseOptions=options
            },
            courseSelected(){
                this.form.import.from_course=this.selectedCourse
                this.submitImport()
            },
            changeMode() {
                this.showFilter = !this.showFilter;
                if (!this.showFilter) {
                     this.init()
                }else{
                    this.scheduleList=[]
                    this.teacherOptions=[]
                    this.courseOptions=[]
                    this.selectedCourse=0
                }
            },
            doSearch(){
                this.selectedCourse=0
                this.scheduleList=[]
                this.courseOptions=[]
                let search=Course.search(this.filterText,true)
                search.then((options) => {
                    this.setCourseOptions(options)
                }).catch(error => {
                    Helper.BusEmitError(error)
                })
            },
            submitImport(){
                let store=ScheduleImport.store(this.form)
                store.then(result => {
                        this.$emit('success')
                    })
                    .catch(error => {
                         this.$emit('failed',error)
                    })

               
            }
        }
     }
</script>