<template>

<div v-if="form">
   <div class="row" v-if="with_user">
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
              <small v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')" class="text-danger"></small>
            </div> 
       </div>
   </div>
   <div class="row" v-if="with_profile">
       <div  class="col-sm-4"> 
           <div class="form-group">                          
              <label>性別</label>
               <div>
                  <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected=onGenderSelected></toggle>
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
                    <input type="hidden" name="user.profile.dob" v-model="form.user.profile.dob">
                    <date-picker  :date="dob" :option="datePickerOption"></date-picker>
                </div>
                <small class="text-danger" v-if="form.errors.has('user.profile.dob')" v-text="form.errors.get('user.profile.dob')"></small>
            </div>
       </div>
   </div>
</div>  
</template>


<script>
  export default {
      name: 'UserInputs',
      props: {
            form: {
               type: Object,
               default: null
            },
            with_user:{
               type:Boolean,
               default:true
            },
            with_profile:{
               type:Boolean,
               default:true
            }
      },
      data() {
            return {
                dob:{
                   time:''
                },
                
                genderOptions:Helper.genderOptions(),
                datePickerOption: Helper.datetimePickerOption(),
            }
      },
      watch:{
            dob: {
              handler: function () {
                  this.onDOBSelected(this.dob.time)
              },
              deep: true
            },
      },
      beforeMount() {
           this.init()
      },
      methods: {
          init() {
             if(this.form.user){
                this.dob.time=this.form.user.profile.dob
              }
          },
          onGenderSelected(val){
             this.$emit('gender-selected',val)
          },
          onDOBSelected(val){
             this.$emit('dob-selected',val)
          },
      }
  }
</script>