<template>
<div>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4>
                    <i class="fa fa-info-circle" aria-hidden="true"></i>  {{ title }}
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
     
</div>
</template>
<script>
    export default {
        name: 'EditSignup',
        props: ['id'],
        components: {
           'date-picker' : MyDatepicker,
            'toggle': Toggle,
        },
        data() {
            return {
                title:'編輯課程報名資訊',
                canEdit:false,
                
                form: new Form({
                    signup: {
                        id:0,
                    }

                }),
                open_date: {
                    time: ''
                },
                close_date: {
                    time: ''
                },
                datePickerOption:{},

                netSignupOptions: [{
                    text: '可',
                    value: '1'
                }, {
                    text: '否',
                    value: '0'
                }],

            }
        },
        watch: {
            id:() => this.init()

        },
        beforeMount() {          
            this.init()
        },
        methods: {
            getId(){
                return this.form.signup.id
            },
            setId(){
                if(this.id){
                    this.form.signup.id=this.id
                }
            },
            init(){

                 this.form = new Form({
                    signup: {}
                })

                this.setId()
                this.fetchData() 
               
                this.datePickerOption=Helper.datetimePickerOption()
            },
            fetchData() {
                let id=this.getId()
               
                let url = '/api/courses/'  
                 if(!id){
                    url += 'create'
                 } else{
                    url += id + '/edit';
                 }        
                axios.get(url)
                    .then(response => {
                        let signup = response.data.course
                        if(signup.cost!=null) signup.cost=Helper.formatMoney(signup.cost)
                        
                        if(signup.tuition!=null)signup.tuition=Helper.formatMoney(signup.tuition)
                        
                        this.form.signup=signup

                        if(signup.open_date){
                             this.open_date.time=signup.open_date
                         }else{
                            let now = Moment();
                            this.open_date.time=now.format('YYYY-MM-DD')   
                        }

                        if(signup.close_date){
                           this.close_date.time=signup.close_date
                        }else{
                            let close=Moment(this.open_date.time).add(1, 'M');
                            this.close_date.time= close.format('YYYY-MM-DD')                 
                        }

                        this.$emit('signupLoaded')
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            setNetSignup(val) {
                this.form.signup.net_signup = val;
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
                this.form.signup.open_date=this.open_date.time
                this.form.signup.close_date=this.close_date.time

                let id=this.getId()
                let url = '/api/courses/' + id + '/updateSignup'
                let method='put'
               
                this.form.submit(method,url)
                    .then(course => {
                        let msg={
                          title:'資料已存檔',
                          status: 200
                       }
                       Bus.$emit('okmsg',msg);   
                       this.$emit('saved')    
                        
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
                this.$emit('endEditSignup')
            }




        },

    }
</script>