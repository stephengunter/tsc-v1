<template>
    <edit-lesson :course_id="course_id" 
      @canceled="onCanceled" @saved="onSaved">
          
    </edit-lesson>
</template>

<script>
    import EditLesson from '../../components/lesson/edit.vue'

    export default {
        name: 'LessonCreate',
        components: {
            'edit-lesson':EditLesson
        },
        props: {
            course_id:{
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