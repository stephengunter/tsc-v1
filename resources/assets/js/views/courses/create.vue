<template>
    <!-- <edit-course :course_id="course_id" :user_id="user_id"
      @canceled="onCanceled" @saved="onSaved">
          
    </edit-course> -->
</template>

<script>
    import EditCourse from '../../components/course/edit.vue'

    export default {
        name: 'CourseCreate',
        components: {
            'edit-course':EditCourse
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