<template>
<div>
    
   <user-checker v-if="checker" :version="userCheckerSettings.version"
      :status="userCheckerSettings.status" 
      @checked="onUserChecked"  @check-failed="onUserCheckFailed" >
   
   </user-checker>
   <div class="row" v-else>
      <div  class="col-sm-4"> 
         <div class="form-group"> 
            <label>姓名</label>
            <input type="text" name="user.profile.fullname" class="form-control" v-model="form.user.profile.fullname" >
            <small v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')" class="text-danger"></small>
         </div> 
      </div>  
      <div  class="col-sm-4"> 
          <div class="form-group"> 
            <label>Email</label>
            <input type="text" name="user.email" class="form-control" v-model="form.user.email" >
            <small v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')" class="text-danger"></small>
         </div> 
      </div>
      <div  class="col-sm-4"> 
         <div class="form-group">
            <label>手機</label>
            <input type="text" name="user.phone" class="form-control" v-model="form.user.phone" >
            <small v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')" class="text-danger" ></small>
            
         </div>
      </div>
   </div>
   <div class="row" v-show="user_checked">
      <div  class="col-sm-4"> 
         <div class="form-group">                          
            <label>性別</label>
            <div>
               <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected="setGender"></toggle>
            </div>
         </div>
      </div>  
      <div  class="col-sm-4"> 
         <div class="form-group">                          
            <label>身分證號</label>
            <input type="text" name="user.profile.SID" class="form-control" v-model="form.user.profile.SID" >
            
            <small v-if="form.errors.has('user.profile.SID')" v-text="form.errors.get('user.profile.SID')" class="text-danger" >身分證號</small>
               
         </div>
      </div>
      <div  class="col-sm-4"> 
         <div class="form-group">
            <label>生日</label>
            <div>
               <date-picker  :date="dob" :option="datePickerOption"></date-picker>
            </div>
            <input type="hidden" name="user.profile.dob" class="form-control" v-model="form.user.profile.dob"  >
            <small class="text-danger" v-if="form.errors.has('user.profile.dob')" v-text="form.errors.get('user.profile.dob')"></small>
         </div>
      </div>
   </div>
                
 </div>       
</template>

<script>
   import UserChecker from '../../../components/user/checker.vue'
   export default {
      name: 'SignupUserInputs',
      components: {
         'user-checker':UserChecker,
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
				title:Helper.getIcon('Signups')  + '  新學員報名',
			
			
				user_checked: false,
				loaded:false,

				search_params:{
					term:0,
					center:0,
					parent:0,
					sub:0,
					reviewed:1
				},

				pay:0,
				boolOptions:Helper.boolOptions(),
				
			
				dob: {
					time: ''
				},
				dobError:false,

			
				genderOptions:Helper.genderOptions(),
				datePickerOption: Helper.datetimePickerOption(),
				
			
				
				

				userList:[],
				showUserList:false,

				userCheckerSettings:{
					version:0,
					status:0
				},
            
               

         }
      },
      
      watch:{
         
         dob: {
            handler: function () {
               this.form.user.profile.dob=this.dob.time
               this.clearErrorMsg('user.profile.dob')
            },
            deep: true
         },
      },
      beforeMount() {
         this.init()
      },
      methods: {
         
			init() {
				if(!this.checker) this.user_checked=true

				this.dob.time=this.form.user.profile.dob
					
			},
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
			setGender(val) {
					this.form.user.profile.gender = val;
			},


      },
      

   }
</script>