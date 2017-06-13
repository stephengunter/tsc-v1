<template>
    <div v-show="" class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4><i class="fa fa-pencil-square-o" aria-hidden="true"></i>  {{ title }}</h4>  
             </span>           
        </div>
        <div class="panel-body">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-3">
                       <div class="form-group">  
                        <label>課程日</label>
                             <drop-down :value.sync="weekdays" multiple  :options="weekdayOptions" label="text"></drop-down>
                            <small class="text-danger" v-if="form.errors.has('course.weekdays')" v-text="form.errors.get('course.weekdays')"></small>
                        
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">  
                            <label>上課時間</label>
                            <div>
                             <time-picker :minute-interval="10" v-model="on" ></time-picker>
                             </div>
                             <small class="text-danger" v-if="form.errors.has('course.on')" v-text="form.errors.get('course.on')"></small>
                        </div>
                       
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">  
                            <label>下課時間</label>
                            <div>
                                <time-picker :minute-interval="10" v-model="off"></time-picker>
                            </div>
                             <small class="text-danger" v-if="form.errors.has('course.off')"  v-text="form.errors.get('course.off')"></small>
                        </div>
                       
                    </div>
                     <div class="col-sm-3">
                       
                        
                    </div>
                </div>

             
            
                <div class="row">
                   
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                      <button class="btn btn-default" @click.prevent="endEdit">取消</button>
                    </div>

                </div>
                    
                
            </form>
        </div>
    </div>

    
     
</template>
<script>
    export default {
        name: 'EditCourse',
        props: ['id','course_id'],
        components: {
            //'drop-down':DropDown,
            'time-picker':TimePicker,
            
        },
        data() {
            return {
                title:'新增課程時間',
                canEdit:false,
               
                form: new Form({
                    classtime: {
                        id:id,
                        course_id:course_id,                      
                    },
                }),
                           
                weekdayOptions:[],
              
                on:{HH: '15', mm: '00'},
                off:{HH: '16', mm: '00'},
                
                loaded:false

            }
        },
        watch: {
            id:function(){
                this.init()
            },
            course_id: function(){
                this.init()
            },
            on: function (val) {
                let time=Helper.getTimeSelected(val)

                if(time){
                    this.clearErrorMsg('course.on')
                }
            },
            off: function (val) {
                let time=Helper.getTimeSelected(val)

                if(time){
                    this.clearErrorMsg('course.off')
                }
            },
        },
        beforeMount() {          
            this.init()
        },
       
        methods: {
            getId(){
                return this.form.classtime.id
            },
            setId(){
                if(this.id){
                    this.form.classtime.id=this.id
                }
            },
            init(){
                this.loaded=false
               
                this.form = new Form({
                    classtime: {
                        id:id,
                        course_id:course_id,                             
                    },
                })
                this.setId()
                this.fetchData() 

                if(this.id) this.title="編輯課程時間"
                else   this.title="新增課程時間"
            },            
            fetchData() {
                let id=this.getId()
               
                let url = '/api/classtimes/'  
                 if(!id){
                    url += 'create'
                 } else{
                    url += id + '/edit';
                 }        
                axios.get(url)
                    .then(response => {
                        let classtime = response.data.course
                        this.form.classtime=classtime

                        this.weekdayOptions=response.data.weekdayOptions

                        let on=Helper.getTimeobj(classtime.on)
                        if(on){
                             this.on=on
                         }else{
                             this.on={HH: '16', mm: '00'}
                         }
                       let off=Helper.getTimeobj(classtime.off)
                        if(off){
                             this.off=off
                         }else{
                             this.off={HH: '18', mm: '00'}
                         }

                         this.loaded=true
                       
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            getPhoto(photo_id) {
                let url = '/api/photoes/';
                if (photo_id) {
                    url += photo_id
                } else {
                    url += 'defaultCourse'
                }

                axios.get(url)
                    .then(response => {
                        this.photo = response.data.photo
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            setTime(){
                let timeOn=Helper.getTimeSelected(this.on)
                if(timeOn){
                    this.form.course.on=timeOn
                }else{
                   this.form.course.on=''
                }

                let timeOff=Helper.getTimeSelected(this.off)
                if(timeOff){
                    this.form.course.off=timeOff
                }else{
                   this.form.course.off=''
                }
            },
            
            setValues(){
                 this.form.course.begin_date=this.begin_date.time
                 this.form.course.end_date=this.end_date.time
                 this.setTime()
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
                this.setValues()

                let url = '/api/courses';
                let method='post'
                let id=this.getId()
                if(id){
                    method='put'
                    url += '/' + id 
                } 
               
                this.form.submit(method,url)
                    .then(course => {
                         let msg={
                          title:'資料已存檔',
                          status: 200
                         }
                        Bus.$emit('okmsg',msg);  

                        this.$emit('saved',course)
                    })
                    .catch(error => {
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
            endEdit(){
                this.$emit('endEditCourse')
            }




        },

    }
</script>