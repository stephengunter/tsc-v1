<template>
     <div class="panel panel-default">
        <div class="panel-heading">
            <span  class="panel-title">
                 <span  class="panel-title">
                    <h4><i class="fa fa-calendar-o" aria-hidden="true"></i> 課程進度</h4>
                  
                  </span>
            </span>
            
            <div>
             
            </div>
            <div class="form-inline">   
                <select v-model="teacher_id"  @change="teacherChanged" class="form-control" >
                    <option v-for="item in teacherOptions" :value="item.value" v-text="item.text"></option>
                </select>
                 &nbsp;&nbsp;
                <select v-model="selectedId"  @change="fetchData" class="form-control" >
                    <option v-for="item in courseOptions" :value="item.value" v-text="item.text"></option>
                </select>
                &nbsp;&nbsp;
                <button type="submit" @click="courseSelected" class="btn btn-success" :disabled="selectedId<1">確認送出</button>
            </div>
        </div>
        <div v-if="loaded" class="panel-body">
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
               
                <tr v-for="schedule in scheduleList"> 
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
        props:['course_id'],
        name: 'TeacherCourse',
        components: {
             
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.scheduleList.length) return true
                return false    
            }
        },
        data() {
            return {
                teacher_id:0, 
                loaded:false,  
                courseOptions:[],             
                scheduleList:[],                
                selectedId:0,
             
            }
        },
        methods: {
            init(){
                this.teacher_id=0
                this.loaded=false  
                this.teacherOptions=[]
                this.courseOptions=[]            
                this.scheduleList=[]                
                this.selectedId=this.course_id

                let teachers=this.loadTeacherOptions()

                teachers.then(() => {

                    let courses =this.loadCourseOptions()
              
                    courses.then(() => {

                       this.fetchData()
                    });
                });
            },
            fetchData(){
                let url = '/api/schedules?course=' + this.selectedId                
                axios.get(url)
                    .then(response => {
                       
                       this.scheduleList=response.data.scheduleList
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            loadTeacherOptions(){
                return new Promise((resolve, reject) => {
                     let url = '/api/teachers/optionsByCourse/' + this.course_id 
                     axios.get(url)
                    .then(response => {
                        this.teacherOptions = response.data.options 
                        this.teacher_id=this.teacherOptions[0].value

                        resolve(true);
                    })
                    .catch(error => {
                        reject(error.response);
                    });
                });
            },
            loadCourseOptions(){
                return new Promise((resolve, reject) => {
                     let url = 'api/courses/optionsByTeacher/' + this.teacher_id 
                     axios.get(url)
                    .then(response => {
                        this.courseOptions = response.data.options 
                        this.selectedId=this.courseOptions[0].value

                        resolve(true);
                    })
                    .catch(error => {
                        reject(error.response);
                    });
                });
            },
            teacherChanged(){
                let courses =this.loadCourseOptions()
              
                      courses.then(() => {

                       this.fetchData()
                });
            },
            courseSelected(){
                this.$emit('courseSelected',this.selectedId);
            }
        }
     }
</script>