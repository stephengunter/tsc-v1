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
      <show v-if="user" v-show="readonly" :user="user"></show>
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
      name: 'SignupUserView',
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
            title:Helper.getIcon('Users') + '  學員資料',
            user:null,

            inputSettings:{
               checker:false
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

            let getUser=User.show(this.user_id)
         
            getUser.then(data => {
               this.user = new User(data.user)
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
            let getUser=User.edit(this.user_id)
         
            getUser.then(data => {
               let user=data.user
               user.role='Student'
               
               this.form=new Form({
                  user:data.user
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
            let updateUser=User.update(this.form,this.user_id)
            
            updateUser.then(user => {
               this.user = new User(user)
               this.readonly=true
            })
            .catch(error => {
               Helper.BusEmitError(error) 
            })
         }
      }
   }
</script>