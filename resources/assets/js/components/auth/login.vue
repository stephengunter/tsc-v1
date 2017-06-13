<template>
<div style="width:650px">
    <div class="panel panel-default" >
        <div class="panel-heading">
            <span class="panel-title">
                <h4><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 使用者登入</h4>
            </span>
            <div>
               
            </div>
            
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
            <form class="form-horizontal" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="form-group">
                    <label  class="col-sm-3 control-label">Email</label>
                    <div class="col-sm-8">
                       <input  name="username" class="form-control" v-model="form.username">
                       <small class="text-danger" v-if="form.errors.has('username')" v-text="form.errors.get('username')"></small>
                    </div>
                </div>
                <div class="form-group">
                    <label  class="col-sm-3 control-label">密碼</label>
                    <div class="col-sm-8">
                       <input type="password" name="password" class="form-control" v-model="form.password">
                       <small class="text-danger" v-if="form.errors.has('password')" v-text="form.errors.get('password')"></small>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-5">
                      <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確定</button>
                      &nbsp;&nbsp;
                      <small class="text-danger" v-if="form.errors.has('msg')" >登入失敗</small>
                      
                    </div>
                    <div>
                          <a @click.prevent="forgotPassword">忘記密碼</a>
                      </div>
                </div>
            </form>
        </div><!-- End panel-body-->
        
    </div>

    

</div>

</template>

<script>
    export default {
        name: 'Login',
        beforeMount() {
           this.init()
        },
        data() {
            return {
                form:{}
            }
        },
        methods: {
            init() {
                this.form=new Form({
                    username:'',
                    password:''
                })
            }, 
            forgotPassword(){
                 this.$emit('forgot-password')
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);

                if(this.form.errors.has('username')) return
                if(this.form.errors.has('password')) return
                this.form.errors.clear()

            },
            onSubmit(){
                let login=this.$auth.login(this.form)
                login.then(result=>{
                    Helper.BusEmitOK('登入成功')
                    this.$emit('logined')            
                }).catch(error=>{
                    if(error.status==439){
                       Helper.redirect('/email-unconfirmed/' + this.form.username)
                    }else{
                       Helper.BusEmitError(error,'登入失敗')
                    } 
                })
              
               
            },
            
            
            
           
        },

    }
</script>