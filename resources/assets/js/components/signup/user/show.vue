<template>
  <div class="panel-body">
      <div class="row">
         <div class="col-sm-4">
            <label class="label-title">真實姓名</label>
            <p> {{ user.profile.fullname }} </p>
         </div> 
         <div class="col-sm-4">
            <label class="label-title">Email</label>
            <p> {{ user.email }} </p>
         </div>  
         <div class="col-sm-4">
            <label class="label-title">手機</label>
            <p> {{ user.phone }} </p>                         
         </div> 
      </div>  <!-- End Row -->
      <div class="row">
         <div class="col-sm-4">
            <label class="label-title">性別</label>
            <p> {{ user.profile.gender | genderText }} </p>                    
         </div> 
         <div class="col-sm-4">
            <label class="label-title">身分證號</label>
            <p> {{ user.profile.SID }} </p>
         </div>   
         <div class="col-sm-4">
            <label class="label-title">生日</label>
            <p> {{ user.profile.dob }} </p>                  
         </div>  
      </div>  <!-- End Row -->
   </div>  
</template>
<script>
   export default {
      name: 'SignupUserShow',
      props: {
         user:{
            type:Object,
            default:null
         }
      },
      methods: { 

      }
   }
</script>