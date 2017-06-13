<template>
<tr v-if="loaded">
     <td> 
        <button class="btn btn-info btn-xs" @click="beginShow">
             <span class="glyphicon glyphicon-link" aria-hidden="true"></span>         
        </button> 
     </td>
     <th scope="row" v-text="lesson.order"></th> 
     <td  v-html="date"></td> 
     <td  v-html="status"></td>
     <td  v-text="time"></td> 
     <td  v-html="position"></td>
    


     <td v-if="!viewMore" v-text="lesson.title"></td> 
     <td v-if="!viewMore" v-text="lesson.content"></td> 
     <td v-if="!viewMore" v-text="lesson.materials"></td> 
     <td v-if="!viewMore" v-text="lesson.ps"></td> 

     <td v-if="viewMore" v-html="teachers"></td> 
     <td v-if="viewMore" v-html="volunteers"></td> 
     <td v-if="viewMore" v-text="lesson.ps"></td> 
    
    
     
</tr>     
</template>


<script>
    import LessonScripts from '../../scripts/lesson.js'
    export default {

        name: 'LessonRow',
        props: ['lesson','viewMore'],
        data() {
            return {
                loaded:false,
                lessonScripts:{},
       
                date:'',
                status:'',
                time:'',
                position:'',
              
                teachers:'',
                volunteers:'',
            
                studentTotal:'',
                studentAttend:'',

            }
        },
        beforeMount() {
            this.init()
           
        },
       
        methods: {
            init(){
                this.loaded=false

                this.lessonScripts=new LessonScripts(this.lesson)
                this.date=this.lessonScripts.dateFormatted()
                this.status=this.lessonScripts.statusLabel()
                this.time=this.lessonScripts.classTimeText()
                this.position=this.lessonScripts.positionText()
              
                this.teachers=this.lessonScripts.teacherNames()
                this.volunteers=this.lessonScripts.volunteerNames()

                this.loaded=true
            },
            beginShow(){
                this.$emit('beginShow',this.lesson.id)
            },
        },

    }
</script>
