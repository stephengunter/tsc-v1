<template>
    <div style="width:600px">
        <div class="panel panel-default" >
            <div class="panel-heading">
                <span class="panel-title">

                    <h4><span class="glyphicon glyphicon-lock" aria-hidden="true"></span> 重設密碼</h4>
                </span>
                <div>
                    
                   
                </div>
                
            </div>  <!-- End panel-heading-->
            <div class="panel-body">
                <form class="form-horizontal" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                    
                    <div class="form-group">
                        <label  class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-8">
                           <input type="email" name="email" class="form-control" v-model="form.email">
                           <small class="text-danger" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-3 control-label">新密碼</label>
                        <div class="col-sm-8">
                           <input type="password" name="password" class="form-control" v-model="form.password">
                           <small class="text-danger" v-if="form.errors.has('password')" v-text="form.errors.get('password')"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-3 control-label">確認新密碼</label>
                        <div class="col-sm-8">
                           <input type="password" name="password_confirmation" class="form-control" v-model="form.password_confirmation">
                           <small class="text-danger" v-if="form.errors.has('password_confirmation')" v-text="form.errors.get('password_confirmation')"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-7">
                          <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確定</button>
                          
                        </div>
                    </div>
                </form>
            </div><!-- End panel-body-->

        </div>
    </div>

    
</div>
</template>

<script>
    export default {
        name: 'ResetPassword',
        props:['user_id' ,'token'],
        beforeMount() {
           this.init()
        },
        data() {
            return {
                form:{},
            }
        },
        methods: {
            init() {
               this.form=new Form({
                     user_id: this.user_id,
                     token: this.token,
                     email:'',
                     password:'',
                     password_confirmation:'',
               })
            }, 
            
            clearErrorMsg(name) {
                this.form.errors.clear(name)
                  
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm(){
               
                let store=this.$auth.resetPassword(this.form)
                
                store.then(data => {
                    Helper.BusEmitOK('重設密碼成功')
                    this.$emit('success')
                })
                .catch(error => {
                    if(error.status!=422){
                        this.$emit('failed')
                    }
                })
               
            }
            
            
           
        },

    }
</script>