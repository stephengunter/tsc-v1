<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">    
             <span class="panel-title">
                   <h4 v-html="title"></h4>
             </span>           
        </div>
        <div class="panel-body">
            <form v-if="loaded" @submit.prevent="onSubmit" class="form-horizontal">
                <div class="form-group">
                    <label  class="col-sm-2 control-label">課程名稱</label>
                    <div class="col-sm-4">
                        <input  name="course_name" class="form-control" :value="course_name" disabled>
                    </div>
                    <label  class="col-sm-2 control-label">狀態</label>
                    <div class="col-sm-4">
                        <div>
                            <input type="hidden" v-model="form.lesson.status"  >
                            <toggle :items="statusOptions"   :default_val="form.lesson.status" @selected=setStatus></toggle>
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
                        <button class="btn btn-default" @click.prevent="onCanceled">取消</button>
                    </div>
                </div>
            </form>
           
        </div>
    </div>
    
</template>
<script>
    
    export default {
        name: 'EditLesson',
        props: {
            id: {
              type: Number,
              default: 0
            },
            course_id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                icon:Helper.getIcon(Lesson.title()),
                title:'',
                loaded:false,
                course:{},
                form: new Form({
                    lesson: {
                      
                    }
                }),
                date: {
                    time: ''
                },
                on:{},
                off:{},
                volunteers:[],
                teachers:[],


                orderOptions:Helper.numberOptions(1,60) ,
                statusOptions:Lesson.statusOptions(),
                datePickerOption:Helper.datetimePickerOption(),

                classroomOptions:[],                
                teacherOptions:[],
                volunteerOptions:[],

                course_name:'',
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
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                this.course={}
                this.form = new Form({
                    lesson: {}
                    
                })
                if(this.id){
                    this.title=this.icon + '  編輯課堂紀錄表'
                }else{
                    this.title=this.icon + '  新增課堂紀錄表'
                }
                this.fetchData() 
            },
            fetchData() {
                let getData=null
                if(this.id){
                    getData=Lesson.edit(this.id)
                }else{
                    getData=Lesson.create(this.course_id)
                }
                getData.then(data=>{
                    let lesson=data.lesson
                    this.form.lesson=data.lesson
                    this.date.time=lesson.date
                    this.on=Helper.getTimeobj(lesson.on)
                    if(!this.on){
                       this.on=Helper.getTimeobj('1600')
                    }
                    this.off=Helper.getTimeobj(lesson.off)
                    if(!this.off){
                       this.off=Helper.getTimeobj('1800')
                    }
                    this.course=data.course
                    this.course_name=Course.getFormatedCourseName(data.course,true)
                    this.teachers=data.teachers
                    this.volunteers=data.volunteers

                   
                    this.classroomOptions=data.classroomOptions
                    this.teacherOptions=data.teacherOptions
                    this.volunteerOptions=data.volunteerOptions

                    

                    this.loaded=true
                }).catch(error=>{
                   Helper.BusEmitError(error)  
                   this.loaded=false
                })  
            },
            idDayOff(){
              let status= parseInt(this.form.lesson.status)
              if(status < 0) return true
              return false
            },
            setStatus(val) {
                this.form.lesson.status = val;
            },
            orderChanged(){
               this.clearErrorMsg('lesson.order')
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                let store=null
                this.form.lesson.date=this.date.time
                this.form.lesson.on=Helper.getTimeSelected(this.on)
                this.form.lesson.off=Helper.getTimeSelected(this.off)

                this.form.lesson.teachers=this.teachers
                this.form.lesson.volunteers=this.volunteers
                
                if(this.id){
                    store=Lesson.update(this.form , this.id)
                }else{
                    store=Lesson.store(this.form)
                }

                // if(this.idDayOff()){
                //    store=Lesson.submitDayoff(this.form)
                // }else{
                //     this.form.lesson.date=this.date.time
                //     this.form.lesson.on=Helper.getTimeSelected(this.on)
                //     this.form.lesson.off=Helper.getTimeSelected(this.off)

                //     this.form.lesson.teachers=this.teachers
                //     this.form.lesson.volunteers=this.volunteers

                //     if(this.id){
                //         store=Lesson.update(this.form , this.id)
                //     }else{
                //         store=Lesson.store(this.form)
                //     }

                // }
                store.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
            onCanceled(){
                this.$emit('canceled')
            }




        },

    }
</script>