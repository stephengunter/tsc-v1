<template>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                  <h4>
                    <i class="fa fa-info-circle" aria-hidden="true"></i>  編輯課程報名資訊
                  </h4> 
             </span>           
        </div>
        <div class="panel-body">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>報名起始日</label>
                            <div>
                                <date-picker :option="datePickerOption" :date="open_date" ></date-picker>
                            </div>
                             <small class="text-danger" v-if="form.errors.has('signup.open_date')" v-text="form.errors.get('signup.open_date')"></small>
                         </div>
                    </div>
                    <div class="col-sm-3">
                       <div class="form-group">                           
                            <label>報名截止日</label>
                            <div>
                                <date-picker :option="datePickerOption" :date="close_date" ></date-picker>
                            </div>
                             <small class="text-danger" v-if="form.errors.has('signup.close_date')" v-text="form.errors.get('signup.close_date')"></small>
                         </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>人數上限</label>
                            <input type="text" name="signup.limit" class="form-control" v-model="form.signup.limit">
                            <small class="text-danger" v-if="form.errors.has('signup.limit')" v-text="form.errors.get('signup.limit')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        
                    </div>
                </div>
                 <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>學費</label>
                            <input type="text" name="signup.tuition" class="form-control" v-model="form.signup.tuition">
                            <small class="text-danger" v-if="form.errors.has('signup.tuition')" v-text="form.errors.get('signup.tuition')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>材料費</label>
                            <input type="text" name="signup.cost" class="form-control" v-model="form.signup.cost">
                            <small class="text-danger" v-if="form.errors.has('signup.cost')" v-text="form.errors.get('signup.cost')"></small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">                           
                            <label>材料</label>
                             <textarea rows="2" cols="50" class="form-control" name="signup.materials"  v-model="form.signup.materials">
                            </textarea>
                            <small class="text-danger" v-if="form.errors.has('signup.materials')" v-text="form.errors.get('signup.materials')"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>網路報名</label>
                            <div>
                            <input type="hidden" v-model="form.signup.net_signup"  >
                             <toggle :items="netSignupOptions"   :defaultVal="form.signup.net_signup" @selected="setNetSignup"></toggle>
                            </div>
                          </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                         </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                         </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                         <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         <button class="btn btn-default" @click.prevent="endEdit">取消</button>
                    </div>
                    <div class="col-sm-4">
                        
                    </div>

                </div>
                    
                
            </form>
            
        </div>
    </div>


   


</template>
<script>
    export default {
        name: 'EditSignupInfo',
        props: {
            id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
               loaded:false,
               
                form: new Form({
                   signupinfo:{}
                }),

                datePickerOption:Helper.datetimePickerOption(),
                open_date: {
                    time: ''
                },
                close_date: {
                    time: ''
                },

                boolOptions:Helper.boolOptions(),

           

            }
        },
        watch:{
            open_date: {
              handler: function () {
                  this.form.signupinfo.open_date=this.open_date.time
                  this.clearErrorMsg('course.open_date')
              },
              deep: true
            },
            close_date: {
              handler: function () {
                  this.form.signupinfo.close_date=this.close_date.time
                  this.clearErrorMsg('course.close_date')
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
                
                this.fetchData() 
             
               
            },
            fetchData() {
                let id=this.id
                let getData=SignupInfo.edit(id)
                
               getData.then(data=>{
                    let signupinfo = new SignupInfo(data.signupinfo)
                    this.form = new Form({
                            signupinfo: signupinfo,
                        })

                    this.open_date.time=signupinfo.begin_date
                    this.close_date.time=signupinfo.end_date

                    this.loaded=true


                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })           
                
                
            },
            
            setNetSignup(val){
                this.form.course.net_signup = val
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
               this.submitForm()
            },
            submitForm() {
                let id=this.id
                let update=SignupInfo.update(this.form, id)
                
                update.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
            cancel(){
                this.$emit('canceled')
            },
            




        },

    }
</script>