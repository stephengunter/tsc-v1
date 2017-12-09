<template>
<div>
    
   <user-checker v-if="checker" :version="userCheckerSettings.version"
      :status="userCheckerSettings.status" 
      @checked="onUserChecked"  @check-failed="onUserCheckFailed" >
   
   </user-checker>
	<div v-else class="row">
       <div class="col-sm-3">
            <div class="form-group"> 
              <label>姓名</label>
              <input type="text" name="user.profile.fullname" class="form-control" v-model="form.user.profile.fullname" >
              <small v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')" class="text-danger"></small>
            </div>  
       </div>
       <div class="col-sm-3">
            <div class="form-group"> 
              <label>Email</label>
              <input type="text" name="user.email" class="form-control" v-model="form.user.email" >
              <small v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')" class="text-danger"></small>
            </div> 
       </div>
       <div class="col-sm-3"> 
           <div class="form-group">                          
              <label>手機</label>
              <input type="text" name="user.phone" class="form-control" v-model="form.user.phone" >
                 
              <small v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')" class="text-danger" ></small>
                 
            </div>
       </div>
    </div>
   <profile-inputs v-if="user_checked" :form="form">
      
   </profile-inputs>
                
 </div>       
</template>

<script>
   import UserChecker from './checker.vue'
   import ProfileInputs from './profile-inputs.vue'
   export default {
      name: 'UserInputs',
      components: {
         'user-checker':UserChecker,
         'profile-inputs':ProfileInputs,
      },
      props: {
         form:{
            type: Object,
            default: {}
         },
         checker:{
            type: Boolean,
            default: true      
         }
      },
      data() {
         return {
               user_checked: false,
               loaded:false,

              
               userCheckerSettings:{
                  version:0,
                  status:0
               },  

         }
      },
      beforeMount() {
          if(!this.checker) this.user_checked=true
      },
      methods: {
         
         checkUser(){
             this.userCheckerSettings.status +=1
         },  
         onUserChecked(user){
            this.user_checked=true
            this.$emit('checked',user)  
         },
         onUserCheckFailed(userList){
             this.$emit('check-failed',userList)  
         },  
         clearErrorMsg(name) {
             this.form.errors.clear(name)
         },
         


      },
      

   }
</script>