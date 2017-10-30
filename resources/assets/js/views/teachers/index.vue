<template>

   
    <teacher-group-list v-if="group"  :hide_create="hide_create" :version="version"  
        :can_select="can_select"
        @selected="onSelected" @begin-create="onBeginCreate">
    </teacher-group-list>
    <teacher-list v-else :hide_create="hide_create" :version="version"  
        :can_select="can_select"
        @selected="onSelected" @begin-create="onBeginCreate">
    </teacher-list>

</template>

<script>
    import TeacherList from '../../components/teacher/list.vue'
    import TeacherGroupList from '../../components/teacher/group-list.vue'

    export default {
        name: 'TeacherIndex',       
        components: {
            'teacher-list':TeacherList,
            'teacher-group-list':TeacherGroupList
        },
        props: {
            group: {
               type: Boolean,
               default: false
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