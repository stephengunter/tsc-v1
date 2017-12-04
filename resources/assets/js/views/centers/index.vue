<template>
    <center-list  :hide_create="hide_create" :version="version"  
        :can_select="can_select" :area_options="area_options"
        @selected="onSelected" @begin-create="onBeginCreate">
    </center-list>

</template>

<script>
    import CenterList from '../../components/center/list.vue'
    

    export default {
        name: 'CenterIndex',       
        components: {
            'center-list':CenterList
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
            area_options:{
               type: Array,
               default: null
            },
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