<template>
   <div class="row">
      <div  class="col-sm-3"> 
         <div class="form-group">                          
            <label>性別</label>
            <div>
               <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected="setGender"></toggle>
               </div>
         </div>
      </div>  
      <div  class="col-sm-3"> 
         <div class="form-group">                          
            <label>身分證號</label>
            <input type="text" name="user.profile.SID" class="form-control" v-model="form.user.profile.SID" >
            
            <small v-if="form.errors.has('user.profile.SID')" v-text="form.errors.get('user.profile.SID')" class="text-danger" >身分證號</small>
               
         </div>
      </div>
      <div  class="col-sm-3"> 
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
</template>

<script>
   
   export default {
      name: 'ProfileInputs',
      props: {
         form:{
            type: Object,
            default: {}
         }
      },
      data() {
         return {
              
               
            
               dob: {
                  time: ''
               },

            
               genderOptions:Helper.genderOptions(),
               datePickerOption: Helper.datetimePickerOption(), 

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
         
      },
      methods: {
         clearErrorMsg(name) {
             this.form.errors.clear(name)
         },
         setGender(val) {
               this.form.user.profile.gender = val;
         },


      },
      

   }
</script>