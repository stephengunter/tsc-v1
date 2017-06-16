<template>
   <tr>
        <td> 
            <button class="btn btn-info btn-xs" @click="selected">
                 <span class="glyphicon glyphicon-link" aria-hidden="true"></span>         
            </button> 
        </td>
        <th scope="row" v-text="lesson.order"></th> 
        <td  v-html="lessonDate()"></td> 
        <td  v-html="statusLabel()"></td>
        <td  v-text="lessonTime()"></td> 
        <td  v-html="position()"></td>

        <td v-if="!more" v-text="lesson.title"></td> 
        <td v-if="!more" v-text="lesson.content"></td> 
        <td v-if="!more" v-text="lesson.materials"></td> 
        <td v-if="!more" v-text="lesson.ps"></td> 

        <td v-if="more" v-html="teachersText()"></td> 
        <td v-if="more" v-html="volunteersText()"></td> 
        <td v-if="more" v-text="lesson.ps"></td> 
        
    </tr>                   
       
</template>
<script>
  
    export default {
        name: 'LessonRow',
        props: {
            lesson: {
               type: Object,
               default: null
            },
            more:{
               type: Boolean,
               default: false
            },
            
        },
        data() {
            return {
                thead:[],
            }
        },
        beforeMount(){
            
        },
        
        methods: {
            lessonDate(){
                return Lesson.dateFormatted(this.lesson.date)
            },
            statusLabel(){
                return Lesson.statusLabel(this.lesson.status)
            },
            lessonTime(){
                return Lesson.lessonClassTimeText(this.lesson)
            },
            position(){
                return Lesson.positionText(this.lesson)
            },
            teachersText(){
                 return Lesson.teacherNames(this.lesson.teachers)             
            },
            volunteersText(){
                return Lesson.volunteerNames(this.lesson.volunteers) 
            },        
            
            // removeItem(id,name){
            //     let values={
            //         id:id,
            //         name:name
            //     }
            //     this.$emit('remove-clicked',values)
            // },
            selected(){
                this.$emit('selected', this.lesson.id)
            },
            unselected(id){
                 this.$emit('unselected',id)
            }
            
            
        },
        
    }
</script>