<template>
   

    <edit-course :course_id="course_id"  :parent="parent"
        @canceled="onCanceled" @saved="onSaved">
    </edit-course>
   

</template>

<script>
    import EditCourse from '../../components/course/edit.vue'
    
    export default {
        name: 'CourseCreate',
        components: {
            'edit-course':EditCourse,
        },
        props: {
            course_id:{
               type: Number,
               default: 0
            },
            parent:{
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
            
            
        },

    }
</script>