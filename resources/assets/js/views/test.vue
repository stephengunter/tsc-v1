<template>
    <div>
         <!-- <button @click="beginEditPhoto(1)">upload</button>
         <button @click="beginEditPhoto(0)">remove</button> -->
        
         <teacher-list v-if="ready" :no_page="listSettings.no_page" 
            :can_edit_number="listSettings.canEditNumber"  :search_params="params"  
            :hide_create="listSettings.hide_create" :version="listSettings.version"  
            :can_select="listSettings.can_select"
            @details="OnDetails"
            @data-loaded="onTeachersLoaded"
            @selected="onSelected" @begin-create="onBeginCreate"
            @group-selected="onGroupSelected">
        </teacher-list>
        
    </div>
</template>

<script>
    import TeacherList from '../components/teacher/list.vue'
    export default {
        name:'Test',
        components: {
           'teacher-list' :TeacherList
        },
        data() {
            return {
                ready:true,
                params:{
                   
                    center:0,
                   
                },
                listSettings:{
                    no_page:true,
                    canEditNumber:true,
                    can_select:false,
                    hide_create:false,
                    version:0
                }
                
                
            }
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
               
            },
            onTeachersLoaded(data){
               this.parentCourse = data.parentCourse
            },
            OnDetails(id){
                this.$emit('details',id)
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onGroupSelected(id){
                this.params.parent=id  
            },
            onBeginCreate(){
                let parent=0
                if(this.parentTeacher) parent= this.parentTeacher.id
                this.$emit('begin-create',parent)
            },
            
            
            
            
            
        }
    }
</script>
