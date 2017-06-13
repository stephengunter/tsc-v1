<template>
<div>
      <refund   :id="id" :can_back="can_back" :version="version"
         @refund-created="onCreated"    @refund-updated="onUpdated" 
         @refund-deleted="onDeleted"    >          
               
      </refund>
</div>    
</template>


<script>
    import Refund from '../../components/refund/refund.vue'

    export default {
        name: 'SignupView',
        components: {
            Refund,
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            hide_create: {
              type: Boolean,
              default: false
            },
            can_back:{
               type: Boolean,
               default: false
            },
        },      
        data() {
            return {
                version:0,
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                
            },
            
            onCreated(refund){
               this.$emit('refund-created',refund)
            },
            onDeleted(refund){
                this.version+=1
                this.$emit('refund-deleted',refund)
            },
            onUpdated(refund){
               this.$emit('refund-updated',refund)
            },
            
        },

    }
</script>