<template>
    <create-signup :course_id="course_id" :user_id="user_id"
      @canceled="onCanceled" @saved="onSaved">
          
    </create-signup>
</template>

<script>
    import CreateSignup from '../../components/signup/create.vue'

    export default {
        name: 'SignupCreate',
        components: {
            'create-signup':CreateSignup
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