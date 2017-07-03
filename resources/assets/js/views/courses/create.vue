<template>
   
    
    <course-import v-if="importing">
    </course-import>

    <edit-course v-else :course_id="course_id" 
      @import="beginImport"
      @canceled="onCanceled" @saved="onSaved">
    </edit-course>
   

</template>

<script>
    import EditCourse from '../../components/course/edit.vue'
    import CourseImport from '../../components/course/import.vue'
    export default {
        name: 'CourseCreate',
        components: {
            'edit-course':EditCourse,
            'course-import':CourseImport
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
                importing:false,
             
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
            onSaved(course){
                this.$emit('saved',course)
            },
            beginImport(){
                this.importing=true
            }
            
           
            
            
        },

    }
</script>