
<template>

<div style="width:600px">
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">

                 <h4>
                    <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
                     忘記密碼
                 </h4>
            </span>
            <div>
               
            </div>
            
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
            <form class="form-horizontal" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="form-group">
                    <label  class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-8">
                       <input type="email" name="email" class="form-control" v-model="form.email">
                       <small class="text-danger" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></small>
                    </div>
                </div>
               
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-5">
                        <button type="submit" class="btn btn-success" :disabled="form.errors.any()">啟動密碼重設程序</button>
                         &nbsp;&nbsp;
                        <small class="text-danger" v-if="form.errors.has('msg')" >啟動密碼重設程序失敗</small>
                      
                    </div>
                    
                </div>
            </form>
        </div><!-- End panel-body-->

    </div>
<div>
</template>

<script>
    export default {
        name: 'ForgotPassword',
        beforeMount() {
           this.init()
        },
        data() {
            return {
                email:'',
                form:{},
            }
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
              
                this.form=new Form({
                    email:'',
                })
            }, 
            clearErrorMsg(name) {
                this.form.errors.clear()
            },
            
            onSubmit(){
                let store=this.$auth.forgotPassword(this.form)
                store.then(result => {
                    this.$emit('success', this.form.email)
                })
                .catch(error => {
                    this.$emit('failed')
                    Helper.BusEmitError(error,'啟動密碼重設程序失敗')

                })
            },
            
            
            
           
        },

    }
</script>