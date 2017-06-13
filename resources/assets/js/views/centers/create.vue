<template>
    <edit
      @canceled="onCanceled" @saved="onSaved">
          
    </edit>
</template>

<script>
    import Edit from '../../components/center/edit.vue'

    export default {
        name: 'CenterCreate',
        components: {
            Edit
        },
        data() {
            return {
                
                
             
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                
             
            },
            onCanceled(){
                this.$emit('canceled')
            },
            onSaved(center){
                
                this.$emit('saved',center)
            },
            
           
            
            
        },

    }
</script>