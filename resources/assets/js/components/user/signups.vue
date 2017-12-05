<template>
   <div>
      <signup-list  v-show="!selected" v-if="indexing"  :user_id="user_id" :can_select="listSettings.can_select"
         :can_pay="listSettings.can_pay"  @selected="onSignupSelected" 
         @pay-clicked="beginPay" @begin-create="onBeginCreateSignup">
      </signup-list>
      <signup-create v-if="creating" :user_id="user_id" 
         @canceled="onCreateCanceled">

      </signup-create>
   </div>
</template>

<script>
import SignupList from '../../components/signup/list.vue'
import SignupCreate from '../../components/signup/create.vue'
export default {
   name:'UserSignups',
   components: {
      'signup-list':SignupList,
      'signup-create':SignupCreate,
           
   },
   props: {
      user_id: {
         type: Number,
         default: 0
      }
            
   },   
   data(){
      return {
         selected:0,
         editting:false,
         listSettings:{
            can_select:false,
            can_pay:true
         }
      } 
   },
   computed: {
      indexing(){
         return  !this.selected  &&  !this.creating
      },
      creating(){
         return  !this.selected  &&  this.editting
      },
   },
   methods:{
      onSignupSelected(id){
                let path=Signup.showUrl(id)
                Helper.newWindow(path)
      },
      onBeginCreateSignup(){
         this.selected=0
         this.editting=true
      },
      onCreateCanceled(){
         this.selected=0
         this.editting=false
      },
      beginPay(){
         alert('begin-pay')
      }
   }
}
</script>

