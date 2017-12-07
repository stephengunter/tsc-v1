<template>
<div>
    <div v-if="!signupId" class="panel panel-default">
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4 v-html="title">
                   </h4>  
             </span> 
             <div>
                  <button   @click="onBack" class="btn btn-default btn-sm" >
                     <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                     返回
                  </button>
              </div>          
        </div>
        <div class="panel-body">
             <signup-index  :hide_create="signupIndexSettings.hide_create"
               :for_refund="signupIndexSettings.for_refund" @selected="onSignupSelected">
             </signup-index>
         </div>
    </div>
    

    <edit-refund v-if="signupId" :id="signupId" :creating="creating"
      @canceled="onCanceled" @saved="onSaved">                
    </edit-refund>
</div>    
</template>

<script>
    import EditRefund from '../../components/refund/edit.vue'

    export default {
        name: 'RefundCreate',
        components: {
          'edit-refund':EditRefund
        },
        props: {
            signup_id: {
              type: Number,
              default: 0
            }
           
        },
        data() {
            return {
                title: Helper.getIcon('Refunds') + '  新增退費申請 - 請先找出對應的報名紀錄',
                creating:true,

                signupIndexSettings:{
                   hide_create:true,
                   for_refund:true
                },
                

                signupId:0,
             
            }
        },
        beforeMount() {
             if(this.signup_id) this.signupId=this.signup_id
        },
        methods: {
            init(){
                this.signupId=0             
            },
            onSignupSelected(id){
                this.signupId=id
            },
            onCanceled(){
                this.init()
                
            },
            onSaved(refund){
                this.$emit('saved',refund)
            },
            onBack(){
                this.$emit('back')
            }
            
           
            
            
        },

    }
</script>