<template>
<div>
     
     <volunteer-list  :hide_create="hide_create" :version="version"  
        :can_select="can_select"
        @selected="onSelected" @begin-create="onBeginCreate">
    </volunteer-list>

</div>

</template>

<script>
    import VolunteerList from '../../components/volunteer/list.vue'
    

    export default {
        name: 'VolunteerIndex',       
        components: {
            'volunteer-list':VolunteerList
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