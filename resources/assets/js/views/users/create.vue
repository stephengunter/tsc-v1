<template>
    <create
      @cenceled="onCanceled" @saved="onSaved">
          
    </create>
</template>

<script>
    import Create from '../../components/user/create.vue'

    export default {
        name: 'UserCreate',
        components: {
            Create
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
            onSaved(user){
                this.$emit('saved',user)
            },
            
           
            
            
        },

    }
</script>