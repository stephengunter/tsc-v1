<template>
    <edit-category :course_id="course_id" 
      @canceled="onCanceled" @saved="onSaved">
          
    </edit-category>
</template>

<script>
    import EditCategory from '../../components/category/edit.vue'

    export default {
        name: 'CategoryCreate',
        components: {
            'edit-category':EditCategory
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