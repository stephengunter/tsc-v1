<template>
    <div>
        <create-by-course :course_id="course_id">
           
        </create-by-course>
        
    </div>
</template>

<script>
    import CreateSignupByCourse from '../components/signup/create-by-course.vue'
   
    export default {
        name:'Test',
        components: {
           'create-by-course':CreateSignupByCourse
        },
        data() {
            return {
                ready:true,
                course_id:1,
                
                
            }
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
               
            },
            onTeachersLoaded(data){
               this.parentCourse = data.parentCourse
            },
            OnDetails(id){
                this.$emit('details',id)
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onGroupSelected(id){
                this.params.parent=id  
            },
            onBeginCreate(){
                let parent=0
                if(this.parentTeacher) parent= this.parentTeacher.id
                this.$emit('begin-create',parent)
            },
            
            
            
            
            
        }
    }
</script>
