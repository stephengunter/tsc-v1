<template>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
            <span class="panel-title">
                  <h4 v-html="title"></h4>
            </span>           
        </div>
        <div v-if="loaded" class="panel-body">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>報名狀態</label>
                            <div>
                                <input type="hidden" v-model="form.status.signup"  >
                                <toggle :items="signupOptions"   :default_val="form.status.signup" @selected="setSignupStatus"></toggle>
                            </div>
                         </div>
                    </div>
                    <div class="col-sm-3">
                         <div class="form-group">                           
                            <label>開課狀態</label>
                            <div>
                                <input type="hidden" v-model="form.status.class"  >
                                <toggle :items="classOptions"   :default_val="form.status.class" @selected="setClassStatus"></toggle>
                            </div>
                         </div>
                       
                    </div>
                    
                </div>
              
                <div class="row">
                    <div class="col-sm-3">
                         <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         <button class="btn btn-default" @click.prevent="cancel">取消</button>
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
        name: 'EditstatusInfo',
        props: {
            course_id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                loaded:false,
                title:Helper.getIcon(CourseStatus.title()) + '  編輯課程狀態' 
                form: {},
               
                signupOptions:[{
                    text: '停止報名',
                    value: '0'
                }, {
                    text: '正常',
                    value: '1'
                }],

                classOptions:[{
                    text: '停止開課',
                    value: '0'
                }, {
                    text: '正常',
                    value: '1'
                }],
            }
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
                let id=this.course_id
                let getData=CourseStatus.edit(id)
                
               getData.then(data=>{
                    this.form=new Form({
                       status:data.status
                    })

                    this.loaded=true


                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })           
                
                
            },
            setSignupStatus(val){
                this.form.signup = val
            },
            setClassStatus(val){
                 this.form.class = val
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
               this.submitForm()
            },
            submitForm() {
                let id=this.id
                let update=CourseStatus.update(this.form, id)
                
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