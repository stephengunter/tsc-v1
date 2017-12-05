<template>
    <create-by-user :user_id="user_id"
      @canceled="onCanceled" @saved="onSaved">
          
    </create-by-user>
</template>

<script>
    import CreateByUser from '../../components/signup/create-by-user.vue'

    export default {
        name: 'SignupCreate',
        components: {
            'create-by-user':CreateByUser
        },
        props: {
            course_id:{
               type: Number,
               default: 0
            },
            user_id:{
               type: Number,
               default: 0
            },
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
            onSaved(){
                this.$emit('saved')
            },
            
           
            
            
        },

    }
</script>