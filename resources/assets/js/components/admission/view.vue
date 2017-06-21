<template>
<div>
    
    <show v-if="readOnly"  :course_id="course_id" can_edit="can_edit"  
         @begin-create="onBeginCreate"  @loaded="onDataLoaded">       
               
    </show>

    <edit v-else :course_id="course_id"
       @saved="onSaved"   @canceled="onEditCanceled" >                 
    </edit> 

  
</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'


    export default {
        name:'AdmissionView',
        components: {
            Show,
            Edit,
        },
        props: {
            course_id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },   
        },
        data() {
            return {
                readOnly:true,
            }
        },
        
        methods: {
            init(){
                this.readOnly=true
            }, 
            onDataLoaded(course){
                 this.$emit('loaded', course)
            },
            onBeginCreate() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onSaved(admission){
                this.init()
                this.$emit('saved',admission)
            },
            
           
            
            
        }
    }
</script>
