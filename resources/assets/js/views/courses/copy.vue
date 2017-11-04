<template>
    
    <div class="panel panel-default">
        <div class="panel-heading ">
            <h4 v-html="title"></h4>
            
            
        </div> <!--  panel  heading -->
        <div  class="panel-body">
            
        </div> <!--  panel  body -->  
    </div>  <!--  panel  -->

   
</template>

<script>
    import CourseList from '../../components/course/list.vue'
    export default {
        name: 'CourseCopy',
        components: {
            'course-list':CourseList
        },
        props: {
            
           
        }, 
        data() {
            return {
                title:Helper.getIcon('Courses')  + '  從舊課程複製',
                
                loading:false,

                err_msg:'',

                


               
            }
        },
        computed:{
           
        
        },
        mounted(){
            this.init()
        },
        methods: {
            init(){
                this.err_msg=''
                this.loading=false
                
            },
            onSubmit(selectedIds){
                let form=new Form({
                    selected_ids:selectedIds,
                })
                let store=Course.import(form)
                store.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('imported')                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
            onCanceled(){
                this.$emit('canceled')
            },
           
            
            
        },

    }
</script>