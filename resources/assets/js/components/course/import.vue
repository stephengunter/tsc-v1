<template>
    
    <course-selector
      :show_title="selectorSettings.show_title"
      :title="selectorSettings.title"
      :default_text="selectorSettings.default_text"
      @submit="onSubmit"
      @canceled="onCanceled"  >
        
    </course-selector>

</template>

<script>
    import CourseFullSelector from './full-selector.vue'
 
    export default {
        name: 'CourseImport',       
        components: {
           'course-selector':CourseFullSelector
        },
        data() {
            return {

                selectorSettings:{
                    show_title:true,
                    title:'<span class="glyphicon glyphicon-import" aria-hidden="true"></span> 從舊課程匯入',
                    default_text:'請選擇需要匯入的舊課程'
                },

             
            }
        },
        
        methods: {
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