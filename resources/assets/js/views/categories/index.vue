<template>
     <category-list  :hide_create="hide_create" 
        :version="currenVersion" :can_select="can_select"
        @selected="onSelected" @begin-create="onBeginCreate">
     </category-list>

</template>

<script>
    import CategoryList from '../../components/category/list.vue'

    export default {
        name: 'CategoryIndex',       
        components: {
            'category-list':CategoryList,
        },
        props: {
            version: {
              type: Number,
              default: 0
            },
            hide_create:{
               type: Boolean,
               default: false
            }
        },
        data() {
            return {
              
                currenVersion:0,
                course_id:0,
               
                can_edit:true,
                can_back:true,
                can_select:false,

                combinationSettings:{
                    withCourse:true,
                },

                initializeSettings:{
                    course_id:0,
                    show:false,
                    width:1000,
                }

             
            }
        },
        watch:{
            version(){
               this.currenVersion+=1
            },
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){

               
             
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onBeginCreate(){
                this.$emit('begin-create',this.course_id)
            },

           
            
        },

    }
</script>