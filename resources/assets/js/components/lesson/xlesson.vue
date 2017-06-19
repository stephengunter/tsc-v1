<template>
<div>
    <lesson-index v-if="indexMode" :course_id="course_id" :canEdit="canEdit"
      @beginCreate="beginCreate" @onBeginShow="beginShow">
    </lesson-index>
    <show v-if="detailsMode" :id="id" @endShow="endShow" @deleted="onDeleted"></show>
    <edit-lesson v-if="creating"  :course_id="course_id"
       @saved="lessonCreated"   @canceled="endCreate" >                 
    </edit-lesson>
</div>

</template>

<script>
    
    import LessonIndex from '../../components/lesson/index.vue'
    import EditLesson from '../../components/lesson/edit-lesson.vue'
    import Show from '../../components/lesson/show.vue'
    export default {
        name: 'Lesson',
        components: {
            'lesson-index':LessonIndex,
             'edit-lesson':EditLesson,
             Show
        },
        props: ['course_id','canEdit'],
        beforeMount() {
           this.init()
        },
        data() {
            return {
                mode:'index',
                id:0,
            }
        },
        computed:{
            creating(){
                return this.mode=='create'    
            },
            indexMode(){
                return this.mode=='index'    
            },
            detailsMode(){
                return this.mode=='details' 
            }
        },
        methods: {
            init() {
                this.mode='index'       
            },  
            beginCreate(){
                this.mode ='create'  
            },
            endCreate(){
                 this.mode='index'    
            },
            lessonCreated(lesson){    
                 this.init()
            },
            beginShow(id){
                this.id=id
                this.mode='details'
            },
            endShow(){
                this.mode='index'   
            },
            onDeleted(){
                 this.init()
            }

        },

    }
</script>