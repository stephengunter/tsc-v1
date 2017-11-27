<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <combination-select :search_params="search_params"
                @ready="onCombinationReady" >
            </combination-select>
            <div v-if="hasCourse" v-html="statusHtml()">
                
            </div>
        </div>
        
     </div>
     
    <signup-list v-if="ready" :course_id="course_id" :hide_create="!canCreate" 
        :version="version" :can_select="can_select" :for_refund="for_refund"
        @loaded="onSignupListLoaded"
        @selected="onSelected" @begin-create="onBeginCreate">
    </signup-list>

    

</div>

</template>

<script>
    import SignupList from '../../components/signup/list.vue'


    export default {
        name: 'SignupIndex',       
        components: {
            'signup-list':SignupList,
        },
        props: {
            version: {
              type: Number,
              default: 0
            },
            hide_create:{
               type: Boolean,
               default: false
            },
            for_refund:{
               type: Boolean,
               default: false
            },
        },
        data() {
            return {
                ready:false,
                course_id:0,
                selectedCourse:null,
               
                can_edit:true,
                can_back:true,
                can_select:false,

                search_params:{
                    term:0,
                    center:0,
                 
                    reviewed:1
                },
             
            }
        },
        computed:{
           hasCourse(){
               if(this.selectedCourse) return true
               return false
           },
           canCreate(){
               if(this.hide_create) return false
               if(!this.hasCourse) return false

               return this.selectedCourse.canSignup
           }
            
        }, 
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                
             
            },
            onCombinationReady(course){
               
                this.setCourse(course)
                this.ready=true
            },
            
            setCourse(val){
               
                this.course_id=val
            },
            onSignupListLoaded(data){
                this.selectedCourse=data.course
            },
            statusHtml(){
                if(!this.hasCourse) return ''

                let html= '課程上限人數：' +  this.selectedCourse.limit  + '&nbsp;'

               return  html + CourseStatus.getSignupLabel(this.selectedCourse.status)
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onBeginCreate(){
                this.$emit('begin-create',this.course_id)
            }
            
            
        },

    }
</script>