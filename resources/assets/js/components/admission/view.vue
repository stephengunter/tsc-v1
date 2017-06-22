<template>
<div>
    
    <show v-if="readOnly"  :course_id="course_id" can_edit="can_edit"  
         @begin-create="onBeginCreate"  @begin-edit="onBeginEdit"
         @loaded="onDataLoaded"
         @selected="onSelected"
         >       
               
    </show>
    
    <edit v-else="editting" :course_id="course_id"
       :creating="creating"
       @saved="onSaved"   @canceled="onEditCanceled" >                 
    </edit> 

  
</div>
</template>
<script>
    import Show from './show.vue'
    import Create from './create.vue'
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
                creating:true
            }
        },
        watch: {
            course_id: function () {
                this.init()
            },
            
        },
        methods: {
            init(){
                this.readOnly=true
                this.creating=true
            }, 
            onDataLoaded(course){
                 this.$emit('loaded', course)
            },
            onBeginCreate() {
                this.creating=true
                this.readOnly=false
            },
            onBeginEdit(){
                this.creating=false
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onSaved(admission){
                this.init()
                this.$emit('saved',admission)
            },
            onSelected(signup_id){
                this.$emit('signup-selected',signup_id)
            },
            
           
            
            
        }
    }
</script>
