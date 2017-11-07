<template>
     
    <admin-list  :options="options"  :can_select="can_select" 
        :hide_create="hide_create" :version="version" 
        @selected="onSelected" @begin-create="onBeginCreate">
    </admin-list>

</template>

<script>
    import AdminList from '../../components/admin/list.vue'
    

    export default {
        name: 'AdminIndex',       
        components: {
            'admin-list':AdminList
        },
        props: {
            options:{
                type:Object,
                default:null
            },
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
                ready:false,
                course_id:0,
               
              
                can_select:false,
             
            }
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
            }
            
            
        },

    }
</script>