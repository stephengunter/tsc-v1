<template>
   <div>
      <signup-list  :user_id="user_id" :can_select="listSettings.can_select"
         :can_pay="listSettings.can_pay"  @selected="onSignupSelected" 
         @pay-clicked="beginPay" @begin-create="onBeginCreateSignup">
      </signup-list>
      
   </div>
</template>

<script>
import SignupList from '../../components/signup/list.vue'
export default {
   name:'UserSignups',
   components: {
      'signup-list':SignupList,
           
   },
   props: {
      user_id: {
         type: Number,
         default: 0
      }
            
   },   
   data(){
      return {
       
         listSettings:{
            can_select:false,
            can_pay:false
         }
      } 
   },
   computed: {
      
     
   },
   methods:{
      onSignupSelected(id){
         let path=Signup.showUrl(id)
         Helper.newWindow(path)
      },
      onBeginCreateSignup(){
         Helper.redirect('/signups/create?user=' + this.user_id )
      },
      onCreateCanceled(){
        
      },
      beginPay(){
         alert('begin-pay')
      }
   }
}
</script>

