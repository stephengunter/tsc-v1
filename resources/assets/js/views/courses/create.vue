<template>
    <div v-show="courseLoaded">
        
        <edit-course @courseLoaded="onCourseLoaded"  @endEditCourse="endEditCourse" @saved="onCourseSaved" :id="id" ></edit-course>
         
       
    </div>
</template>


<script>
    
    import EditCourse from '../../components/course/edit-course.vue'

    export default {
        components:{
            EditCourse,
        },
        name:'CreateCourse',
        data()  {
            return {                
                id:0,
                isReadOnly:false,
                courseLoaded:false,
            }
        },
        beforeMount(){
            this.init()
        },
        methods:{
            init(){
                this.isReadOnly=false
            },
            beginEditcourse() {
                 this.isReadOnly=false
            },
            endEditCourse(){
                this.$router.push('/courses');
            },
            onCourseLoaded(){
                this.courseLoaded=true
            },
            onCourseSaved(course){                
                let url='/courses/' + course.id
                this.$router.push(url);
            },           
           

        }

    }
</script>