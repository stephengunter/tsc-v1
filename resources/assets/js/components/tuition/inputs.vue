<template>

<div v-if="form">
   
   <div class="row">
      <div class="col-sm-3">
         <div class="form-group">                           
            <label>繳費方式</label>
            <select  v-model="form.tuition.pay_by" class="form-control">
               <option v-for="(item,index) in payways" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
         </div>
      </div>
      <div  class="col-sm-3">
         <div class="form-group"> 
            <label>繳費金額</label>
            <input type="text" name="tuition.amount" class="form-control" v-model="form.tuition.amount" @keydown="clearErrorMsg('tuition.amount')"  >
            <small class="text-danger" v-if="form.errors.has('tuition.amount')" v-text="form.errors.get('tuition.amount')"></small>
         </div> 
         
      </div>
      <div  class="col-sm-6">
         <div class="form-group">
            <label>備註</label>
            <input type="text" name="tuition.ps" class="form-control" v-model="form.tuition.ps"   >
           
         </div>
      </div>
   </div>   <!--  row   -->
  
   
</div>  
</template>


<script>
  export default {
      name: 'TuitionInputs',
      props: {
         form: {
            type: Object,
            default: null
         },
         payways:{
            type:Array,
            default:null
         }           
      },
      data() {
            return {
               
               
             
            }
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
         //  onGenderSelected(val){
         //     this.$emit('gender-selected',val)
         //  },
         //  onActiveSelected(val){
         //      this.$emit('active-selected',val)
         //  },
          
         //  onReviewedSelected(val){
         //      this.$emit('reviewed-selected',val)
         //  },
         //  onCanceled(){
         //     this.$emit('canceled')
         //  },
         //  clearDOB(){
         //        this.dob.time=''
         //  },
      }
  }
</script>