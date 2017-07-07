<template>
    <edit-notice :course_id="course_id" 
      @canceled="onCanceled" @saved="onSaved">
          
    </edit-notice>
</template>

<script>
   import EditNotice from '../../components/notice/edit.vue'

    export default {
        name: 'NoticeCreate',
        components: {
            'edit-notice':EditNotice
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