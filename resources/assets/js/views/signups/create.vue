<template>
<div>
    <create-by-user v-if="user_id" :user_id="user_id"
      @canceled="onCanceled" @saved="onSaved">
          
    </create-by-user>
    <create-by-course v-if="course_id"  :course_id="course_id"
      @canceled="onCanceled" @saved="onSaved">
          
    </create-by-course>
</div>    
</template>

<script>
    import CreateByUser from '../../components/signup/create-by-user.vue'
    import CreateByCourse from '../../components/signup/create-by-course.vue'
    export default {
        name: 'SignupCreate',
        components: {
            'create-by-user':CreateByUser,
            'create-by-course':CreateByCourse
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