<template>
    <div style="width:600px">
        <div class="panel panel-default" >
            <div class="panel-heading">
                <span class="panel-title">
                    <h4>
                        <span class="glyphicon glyphicon-lock" aria-hidden="true"></span> 變更密碼
                    </h4>
                </span>
            </div>  <!-- End panel-heading-->
            <div class="panel-body">
                <form class="form-horizontal" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                    
                    <div class="form-group">
                        <label  class="col-sm-3 control-label">舊密碼</label>
                        <div class="col-sm-8">
                           <input type="password" name="current_password" class="form-control" v-model="form.current_password">
                           <small class="text-danger" v-if="form.errors.has('current_password')" v-text="form.errors.get('current_password')"></small>
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
                            <small class="text-danger" v-if="form.errors.has('msg')">變更密碼失敗</small>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                            <button class="btn btn-default" @click.prevent="onCancel">取消</button>
                  
                        </div>
                    </div>
                </form>
            </div><!-- End panel-body-->

        </div>
    </div>

   
</template>

<script>
    export default {
        name: 'ChangePassword',

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
                     current_password:'',
                     password:'',
                     password_confirmation:'',
               })
            }, 
           
            clearErrorMsg(name) {
                this.form.errors.clear(name)
                if(this.form.errors.has('current_password')) return 
                if(this.form.errors.has('password')) return 
                if(this.form.errors.has('password_confirmation')) return 

                this.form.errors.clear()    
            },
            onCancel(){
                this.init()
                this.$emit('canceled')
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm(){
                let store=this.$auth.changePassword(this.form)
                
                store.then(data => {
                    Helper.BusEmitOK('變更密碼成功')
                    this.$emit('success')
                })
                .catch(error => {
                    if(error.status!=422){
                        let errors={}
                        errors['msg']=['變更密碼失敗']
                        this.form.onFail(errors)
                    }
                   
                })
               
            }
            
            
           
        },

    }
</script>