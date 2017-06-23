<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">    
             <span class="panel-title">
                   <h4 v-html="title"></h4>
             </span>           
        </div>
        <div  v-if="loaded"  class="panel-body">  
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>姓名</label>
                            <div class="form-inline">
                                <input type="text"  v-model="form.student.user.profile.fullname"  class="form-control" disabled >
                                   &nbsp;
                                 <button @click.prevent="onEditUser" class="btn btn-primary btn-xs">
                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                </button>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-sm-1">
                        
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">                           
                            <label>加入日期</label>
                            <div>
                              
                                <date-picker :option="datePickerOption" :date="join_date" ></date-picker>
                           
                            </div>
                        </div>  
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">                           
                            <label>狀態</label>
                            <div>
                               <input type="hidden" v-model="form.student.active"  >
                               <toggle :items="activeOptions"   :default_val="form.student.active" @selected=setActive></toggle>
                            </div>
                        </div>  
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">                           
                            <label>備註</label>
                            <input type="text" v-model="form.student.ps"  class="form-control">
                        </div>
                    </div>
                    
                </div>
            
                <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group">                           
                            <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             <button class="btn btn-default" @click.prevent="onCanceled">取消</button>  
                         </div>
                    </div>
                    
                </div><!-- </div> end row -->
                    
                
            </form>
           
        </div>
    </div>
    
</template>
<script>
    
    export default {
        name: 'EditStudent',
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
                title:Helper.getIcon(Student.title())  + '  編輯學員資料',
                loaded:false,
              
                form: {},
                
                datePickerOption:Helper.datetimePickerOption(),
                join_date: {
                    time: ''
                },
                activeOptions: Student.activeOptions(),
            }
        },
        watch:{
            join_date: {
              handler: function () {
                  this.form.student.join_date=this.join_date.time
                 
              },
              deep: true
            },
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                this.form = new Form({
                    student: {}
                    
                })
             
                this.fetchData() 
            },
            fetchData() {
                let getData=Student.edit(this.id)
                getData.then(data=>{
                    let student=data.student
                    this.form.student=data.student
                    this.join_date.time=student.join_date
                    this.loaded=true
                }).catch(error=>{
                   Helper.BusEmitError(error)  
                   this.loaded=false
                })  
            },
            
            setActive(val){
                this.form.student.active=val
            },
            onEditUser(){
                 this.$emit('edit-user', this.form.student.user_id)
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                let update=Student.update(this.form, this.id)
               
                update.then(data => {
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