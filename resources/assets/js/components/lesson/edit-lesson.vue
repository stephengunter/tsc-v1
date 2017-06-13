<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4>
                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i> {{title}}
                   </h4>  
             </span>           
        </div>
        <div class="panel-body">
            <form v-if="loaded" @submit.prevent="onSubmit" class="form-horizontal">
                    <div class="form-group">
                      <label  class="col-sm-2 control-label">課程名稱</label>
                      <div class="col-sm-4">
                         <select v-model="form.lesson.course_id"  class="form-control" >
                              <option :value="course.id" v-text="courseNameText"></option>
                         </select>
                      </div>
                     <label  class="col-sm-2 control-label">狀態</label>
                      <div class="col-sm-4">
                         <div>
                              <input type="hidden" v-model="form.lesson.status"  >
                               <toggle :items="statusOptions"   :defaultVal="form.lesson.status" @selected=setStatus></toggle>
                         </div>
                       </div>
                    
                  </div>
                  <div class="form-group" >
                    <label  class="col-sm-2 control-label">課堂日期</label>
                    <div class="col-sm-4">
                        <date-picker :option="datePickerOption" :date="date" ></date-picker>
                        <small class="text-danger" v-if="form.errors.has('lesson.date')" v-text="form.errors.get('lesson.date')"></small>
                    </div>
                    <div v-show="!isbreak">
                    <label  class="col-sm-2 control-label">順序</label>
                    <div class="col-sm-4">
                        <select @change="orderChanged"  v-model="form.lesson.order"  class="form-control" >
                           <option v-for="item in orderOptions" :value="item.value" v-text="item.text"></option>
                        </select>
                       <small class="text-danger" v-if="form.errors.has('lesson.order')" v-text="form.errors.get('lesson.order')"></small>
                    </div>
                    </div>
                  </div>
                  <div class="form-group" v-show="!isbreak">
                    <label  class="col-sm-2 control-label">教室</label>
                    <div class="col-sm-4">
                        <select v-model="form.lesson.classroom_id" class="form-control" >
                           <option v-for="item in classroomOptions" :value="item.value" v-text="item.text"></option>
                        </select>
                       
                    </div>
                    <label  class="col-sm-2 control-label">上課時間</label>
                    <div class="col-sm-4">
                        <time-picker :minute-interval="10" v-model="on" ></time-picker>
                        <small class="text-danger" v-if="form.errors.has('lesson.on')" v-text="form.errors.get('lesson.on')"></small>
                    
                          &nbsp;  - &nbsp;

                        <time-picker :minute-interval="10" v-model="off" ></time-picker>
                        <small class="text-danger" v-if="form.errors.has('lesson.off')" v-text="form.errors.get('lesson.off')"></small>
                    
                    </div>
                    
                    
                  </div>
                  <div class="form-group"  v-show="!isbreak">
                    <label  class="col-sm-2 control-label">授課教師</label>
                    <div class="col-sm-4">
                       <drop-down :value.sync="teachers" multiple  :options="teacherOptions" label="text"></drop-down>
                       <small class="text-danger" v-if="form.errors.has('lesson.teachers')" v-text="form.errors.get('lesson.teachers')"></small>
                    </div>
                    <label  class="col-sm-2 control-label">教育志工</label>
                    <div class="col-sm-4">
                      <drop-down :value.sync="volunteers" multiple  :options="volunteerOptions" label="text"></drop-down>
                      <small class="text-danger" v-if="form.errors.has('lesson.volunteers')" v-text="form.errors.get('lesson.volunteers')"></small>
                    </div>
                  </div>

                  <div class="form-group" v-show="!isbreak">
                    <label  class="col-sm-2 control-label">課目標題</label>
                    <div class="col-sm-4">
                      <textarea rows="3" cols="50" class="form-control" name="lesson.title"   v-model="form.lesson.title" >
                      </textarea>
                       <small class="text-danger" v-if="form.errors.has('lesson.title')" v-text="form.errors.get('lesson.title')"></small>
                    </div>
                    <label  class="col-sm-2 control-label">內容重點</label>
                    <div class="col-sm-4">
                      <textarea rows="3" cols="50" class="form-control" name="lesson.content"   v-model="form.lesson.content" >
                      </textarea>
                      <small class="text-danger" v-if="form.errors.has('lesson.content')" v-text="form.errors.get('lesson.content')"></small>
                    </div>
                  </div>
                   <div class="form-group" v-show="!isbreak">
                    <label  class="col-sm-2 control-label">教材</label>
                    <div class="col-sm-10">
                       <input type="text" name="lesson.materials" class="form-control" v-model="form.lesson.materials">
                    </div>
                  </div>

                  
                 
                   <div class="form-group">
                    <label  class="col-sm-2 control-label">備註</label>
                    <div class="col-sm-10">
                       <input type="text" name="lesson.ps" class="form-control" v-model="form.lesson.ps">
                    </div>
                  </div>
                
                  
                  
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確定</button>
                       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button class="btn btn-default" @click.prevent="canceled">取消</button>
                    </div>
                  </div>
            </form>
        </div>
    </div>
    
</template>
<script>
    export default {
        name: 'EditLesson',
        props: ['id','course_id'],
        components: {
             'toggle': Toggle,
             'drop-down':DropDown,
             'date-picker' : MyDatepicker,
             'time-picker':TimePicker,
        },
        data() {
            return {
                title:'編輯課堂紀錄表',
                loaded:false,
                course:{},
                date: {
                    time: ''
                },
                on:{},
                off:{},

                statusOptions: [{
                    text: '一般',
                    value: '0'
                }, {
                    text: '已結束',
                    value: '1'
                },{
                    text: '停課',
                    value: '-1'
                }],
                
                form: new Form({
                   lesson:{}
                }),

                volunteers:[],
                teachers:[],

                datePickerOption:{},
                classroomOptions:[],
                orderOptions:[],
                teacherOptions:[],
                volunteerOptions:[],

            }
        },
        watch:{
            on: function (val) {
                if(!val ) return false
                let time=Helper.getTimeSelected(val)
                
                if(time){
                    this.clearErrorMsg('lesson.on')
                }
            },
            off: function (val) {
                if(!val ) return false
                let time=Helper.getTimeSelected(val)

                if(time){
                    this.clearErrorMsg('lesson.off')
                }
            },
            volunteers: function (val) {
               if(val && val.length){
                 this.clearErrorMsg('lesson.volunteers')
               }
            },
            teachers: function (val) {
               if(val && val.length){
                 this.clearErrorMsg('lesson.teachers')
               }
            },
        },
        computed: {
           courseNameText() {
                return this.course.number + ' ' + this.course.name
            },
            classCount(){
                if(!this.classTimes.length) return 0
                return this.classTimes.length * this.course.weeks
            },
            isbreak(){
               if(this.form.lesson.status<0)return true
               return false
            },
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
                this.classroomOptions=[]
                this.form = new Form({
                    lesson:{}
                })

                this.date= {
                    time: ''
                }

                this.datePickerOption=Helper.datetimePickerOption()
                this.loadOrderOptions()

                if(this.id>0){
                  this.title= '編輯課堂紀錄表'
                }else{
                  this.title= '新增課堂紀錄表'
                }
                this.fetchData()  
            }, 
            fetchData() {
                let id=this.id
                let url = '/api/lessons/'  
                 if(!id){
                    url += 'create?course=' + this.course_id
                 } else{
                    url += id + '/edit';
                 }   
                      
                axios.get(url)
                    .then(response => {
                        let lesson=response.data.lesson
                        this.form = new Form({
                            lesson:lesson
                        })
                        this.date.time=lesson.date
                        this.on=Helper.getTimeobj(lesson.on)
                        if(!this.on){
                          this.on=Helper.getTimeobj('1600')
                        }
                        this.off=Helper.getTimeobj(lesson.off)
                        if(!this.off){
                          this.off=Helper.getTimeobj('1800')
                        }


                        this.course=response.data.course

                        this.teachers=response.data.teachers
                        this.volunteers=response.data.volunteers

                       
                        this.classroomOptions=response.data.classroomOptions
                        this.volunteerOptions=response.data.volunteerOptions
                        this.teacherOptions=response.data.teacherOptions

                        this.loaded=true
                       
                    })
                    .catch(function(error) {
                        console.log(error)                            
                    })
            },
            
            beginDateFullText(){

                let date= this.course.begin_date
                 
                let formated=true
                return date + '&nbsp' + Helper.chineseDayofWeek(date,formated)
            },
            
            loadOrderOptions(){
               if(!this.orderOptions.length){
                    this.orderOptions=Helper.numberOptions(1,60)       
                }
            },  
            idDayOff(){
              let status= parseInt(this.form.lesson.status)
              if(status < 0) return true
              return false
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
                if(this.idDayOff()){
                   return this.submitDayOff()
                }

                let url = '/api/lessons'
                let method='post'

                let id=this.id
                if(id){
                    method='put'
                    url += '/' + id 
                }
                this.form.lesson.date=this.date.time
                this.form.lesson.on=Helper.getTimeSelected(this.on)
                this.form.lesson.off=Helper.getTimeSelected(this.off)

                this.form.lesson.teachers=this.teachers
                this.form.lesson.volunteers=this.volunteers

                this.form.submit(method,url)
                    .then(lesson => {
                       Helper.BusEmitOK()
                       this.$emit('saved',lesson)                            
                    })
                    .catch(error => {
                        Helper.BusEmitError(error) 
                    })
            },
            submitDayOff(){
                let url = '/api/lessons/dayOff'
                let method='post'
                
                this.form.lesson.date=this.date.time

                this.form.lesson.order=''
                this.form.lesson.title=''
                this.form.lesson.content=''
                this.form.lesson.materials=''
                this.form.lesson.classroom_id=''
                this.form.lesson.on=''
                this.form.lesson.off=''


                this.form.submit(method,url)
                    .then(lesson => {
                       this.$emit('saved',lesson)
                       Helper.BusEmitOK()    
                    })
                    .catch(error => {
                         Helper.BusEmitError(error)  
                    })
            },
            orderChanged(){

              this.clearErrorMsg('lesson.order')
            },
            clearErrorMsg(name) {

                this.form.errors.clear(name);
            },
            setStatus(val) {
                this.form.lesson.status = val;
            },
            canceled(){
                this.$emit('canceled')
            }




        },

    }
</script>