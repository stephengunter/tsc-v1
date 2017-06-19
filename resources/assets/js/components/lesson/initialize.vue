<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4>
                        <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
                        初始化課堂紀錄表
                   </h4>  
             </span>           
        </div>
        <div class="panel-body">
            <form v-if="loaded" @submit.prevent="onSubmit" class="form-horizontal">
                    <div class="form-group">
                    <label  class="col-sm-2 control-label"></label>
                    <div class="col-sm-6">
                       <h3><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        請確認以下資料是否正確
                       </h3>
                    </div>
                  </div> -  



                  <div class="form-group">
                    <label  class="col-sm-2 control-label">課程名稱</label>
                    <div class="col-sm-6">
                        <input type="text" name="course" :value="courseName"  class="form-control"  disabled>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label">上課地點</label>
                    <div class="col-sm-6 form-inline">
                       <select>
                            <option v-text="course.center.name"></option>
                       </select> &nbsp;&nbsp;&nbsp;
                       <select v-model="form.classroom_id" class="form-control" >
                           <option v-for="item in classroomOptions" :value="item.value" v-text="item.text"></option>
                       </select>
                    </div>
                  </div>
                  <div class="form-group">
                        <label  class="col-sm-2 control-label">上課時間</label>
                        <div class="col-sm-6">
                            <label  v-for="item in classTimes" 
                            style="font-size:17px" class="control-label" 
                            v-html="classTimeFullText(item)">
                                
                            </label>
                            <small v-if="form.errors.has('classTimes')" v-text="form.errors.get('classTimes')" class="text-danger" ></small>
                        </div>
                  </div>
                  <div class="form-group">
                        <label  class="col-sm-2 control-label">開始日期</label>
                        <div class="col-sm-6">
                            <label style="font-size:17px" class="control-label" v-html="beginDateFullText"></label>
                            <small v-if="form.errors.has('begin_date')" v-text="form.errors.get('begin_date')" class="text-danger" ></small>
                        </div>
                  </div>
                  <div class="form-group">
                        <label  class="col-sm-2 control-label">週數</label>
                        <div class="col-sm-6">
                            <label style="font-size:17px" class="control-label" v-text="course.weeks"></label>                            
                        </div>
                  </div>
                  <div class="form-group">
                        <label  class="col-sm-2 control-label">次數</label>
                        <div class="col-sm-1">
                            <label style="font-size:17px" class="control-label" v-text="classCount"></label>                            
                        </div>
                        <label  class="col-sm-2 control-label">預定進度數</label>
                        <div class="col-sm-3">
                            <label style="font-size:17px" class="control-label" v-text="this.course.schedules.length"></label>                            
                        </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確定</button>
                       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button class="btn btn-default" @click.prevent="initializeCanceled">取消</button>
                    </div>
                  </div>
            </form>
        </div>
    </div>
    
</template>
<script>
    export default {
        name: 'InitializeLessons',
        props: {
            course_id: {
              type: Number,
              default: 0
            }
        },
        data() {
            return {
                loaded:false,
                course:{},
                classTimes:[],
                courseName:'',
                form: new Form({
                    course_id: 0,
                    classroom_id:0
                }),
                classroomOptions:[],
            }
        },
        computed: {
            classCount(){
                if(!this.classTimes.length) return 0
                  return this.classTimes.length * this.course.weeks
            },
            beginDateFullText(){

                  let date= this.course.begin_date
                 
                  let formated=true
                return date + '&nbsp' + Helper.chineseDayofWeek(date,formated)
            }
        },
        created(){
             
        },
        beforeMount() {
            this.init()
        },
       
        methods: {
            init() {
              
                this.loaded=false
                this.course={}
                this.classTimes=[]
                this.classroomOptions=[]
                this.form = new Form({
                    course_id:this.course_id,
                    classroom_id:0
                })
                this.fetchData()  
            }, 
            fetchData() {
                let create=Lesson.createInitialize(this.course_id)
              
                create.then(data => {
                    this.classTimes=data.classTimes
                    let course = data.course
                    this.classroomOptions=data.classroomOptions
                    this.form.classroom_id=data.classroomOptions[0].value
                    this.course=course
                    this.courseName=Course.getFormatedCourseName(course,true)

                    this.loaded=true
                   
                })
                .catch(error => {
                       Helper.BusEmitError(error)
                })
            },
            
            classTimeFullText(classTime){
              
               return Helper.classTimeFullText(classTime) +   '&nbsp;&nbsp;&nbsp;'             
            },
            onSubmit() {
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.submitForm()
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
            },
            submitForm() {
                let url = '/api/lessons/initialize'
                let method='post'

                this.form.submit(method,url)
                    .then(result => {
                       this.$emit('saved')
                       let msg={
                          title:'資料已存檔',
                          status: 200
                       }
                       
                       Bus.$emit('okmsg',msg);       
                    })
                    .catch(error => {
                        console.log(error)
                        let msgtitle = '存檔失敗'
                        if (error.data.msgtitle) {
                            msgtitle = error.data.msgtitle;
                        }

                        Bus.$emit('errors', {
                                title: msgtitle,
                                status: error.status
                        })
                           
                    })
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            
            initializeCanceled(){
                this.$emit('initializeCanceled')
            }




        },

    }
</script>