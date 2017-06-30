<template>
   <tr>
        <td>
           <a href="#" @click.prevent="selected">
           {{ course.number  }} &nbsp; {{ course.name  }}
           </a>
        </td>
        <td v-html="period(course.begin_date , course.end_date)"></td> 
        <td v-html="period(course.open_date , course.close_date)"></td> 
        <td v-html="$options.filters.reviewedLabel(course.reviewed)" ></td> 
        <td v-html="$options.filters.activeLabel(course.active)" ></td> 
        <td v-html="signupLabel()"></td>  
        <td v-html="registerLabel()"></td>  
        <td v-html="classLabel()"></td>              
    </tr>                   
       
</template>
<script>
  
    export default {
        name: 'CourseRow',
        props: {
            course: {
               type: Object,
               default: null
            },
            more:{
               type: Boolean,
               default: false
            },
            remove:{
               type: Boolean,
               default: false
            },
            select:{
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
            
            signupLabel(){
                return CourseStatus.getSignupLabel(this.course.status)
            },
            registerLabel(){
                return CourseStatus.getRegisterLabel(this.course.status)
            },
            classLabel(){
                return CourseStatus.getClassLabel(this.course.status)
            },
            
            period(begin,end){
               return Helper.period(begin,end)
            },
            
            selected(){
                this.$emit('selected',this.course.id)
            },
            
            
            
        },
        
    }
</script>