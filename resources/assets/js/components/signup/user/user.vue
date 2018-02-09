<template>
<div class="row">
   <div v-show="!userSelected"  class="col-sm-6" style=" padding-bottom: 1cm;">
      <label>姓名</label>
      <div class="input-group">
         <drop-down  v-model="selectedUser" :options="user_options" label="text" ></drop-down>
         <a :href="newUserSignupUrl()" class="input-group-addon" >新學員報名</a>
      </div>
   </div>
   <div class="col-sm-12">
      <user-view v-if="selectedUser" ref="userview" :user_id="selectedUser.value" 
         :can_back="userViewSettings.can_back" @canceled="clearUser">
      </user-view>
   </div>
</div>   
</template>

<script>
   import UserView from './view.vue'
   export default {
      name: 'SignupUser',
      components: {
         'user-view':UserView
      },
      props: {
         user_options:{
            type: Array,
            default: null
         },
         user:{
            type: Object,
            default: null
         },
      },   
      data() {
         return {
               
            selectedUser:null,
            
            userViewSettings:{
               can_back:true
            },
            
         }
      },
      computed:{
         userSelected(){
            if(!this.selectedUser) return false
            if(!this.selectedUser.value)  return false
            return true
         }
      },
      watch: {
         selectedUser(){
            this.$emit('user-changed', this.selectedUser)
               // let user_id=0
               // if(this.selectedUser) {
               //      user_id=Helper.tryParseInt(this.selectedUser.value)
               // } 
               
               // this.signup.user_id=user_id

               // this.clearErrorMsg('signup.user_id') 
         },
         
   
      },
      beforeMount() {
         this.init()
      },
      methods: {
         init(){
            if(this.user){
               this.selectedUser= Object.assign({}, this.user)
            }
           
         },
         newUserSignupUrl(){
               let url= '/signups/new-user'
               if(this.course_id) url +='?course=' + this.course_id
               return url
         },
         clearUser(){
            this.selectedUser=null
         },
         
      },

   }
</script>