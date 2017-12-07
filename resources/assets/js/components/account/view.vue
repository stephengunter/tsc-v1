<template>
   <div class="panel panel-default">
      <div class="panel-heading">
         <span class="panel-title">
            <h4 v-html="title"></h4>
         </span> 
              
         <div>
            <button v-if="can_back" v-show="readonly" @click.prevent="canceled" class="btn btn-default btn-sm" >
                 <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                     返回
             </button>
            <button v-if="readonly" @click.prevent="edit" class="btn btn-primary btn-sm" >
               <span class="glyphicon glyphicon-pencil"></span> 編輯
            </button>
           
         </div>
      </div>  <!-- End panel-heading-->
      <show v-if="account" v-show="readonly" :account="account"></show>
      <div v-if="!readonly" class="panel-body"> 
         <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
            <inputs :checker="inputSettings.checker" :form="form"></inputs>
            <div class="row">
               <div  class="col-sm-6"> 
                  <div class="form-group">                           
                     <button type="submit" class="btn btn-success" >確認送出</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <button class="btn btn-default" @click.prevent="init">取消</button>  
                  </div>
               </div>
            </div>
         </form>  
      </div>     
   </div>
   
    
</template>
<script>
   import Show from './show.vue'
   import Inputs from './inputs.vue' 
   export default {
      name: 'BankAccountView',
      components: {
         'show':Show,
         'inputs':Inputs
      },
      props: {
         user_id:{
            type:Number,
            default:0
         },
         can_back:{
            type:Boolean,
            default:false
         },
      },
      data() {
         return {
            readonly:true,
            title:Helper.getIcon('tuitions') + '  銀行帳號',
            account:null,

            inputSettings:{
              
            },

            form:{}
         }
      },
      beforeMount() {
        this.init()
      },
      methods: { 
         init(){
            this.readonly=true

            let getData=Account.show(this.user_id)
         
            getData.then(data => {
               this.account = data.account
            })
            .catch(error=> {
              
               Helper.BusEmitError(error)
            })
			},
			isBusy(){
				return !this.readonly
			},
			canceled(){
				this.$emit('canceled')
			},
         edit(){
            let getData=Account.edit(this.user_id)
         
            getData.then(data => {
               let account=data.account
               
               this.form=new Form({
                  account:data.account
               })
               this.readonly=false
            })
            .catch(error=> {
              
               Helper.BusEmitError(error)
            })
         },
         clearErrorMsg(name) {
            this.form.errors.clear(name)
         },
         onSubmit(){
				let id=this.form.account.id
            let update=Account.update(this.form, id)
            
            update.then(account => {
               this.account = account
               this.readonly=true
            })
            .catch(error => {
               Helper.BusEmitError(error) 
            })
         }
      }
   }
</script>