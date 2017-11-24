<template>
    <div>
        <new-user-signup>
           
        </new-user-signup>
        
    </div>
</template>

<script>
    import CreateSignup from '../components/signup/create.vue'
   
    export default {
        name:'Test',
        components: {
           'create-signup':CreateSignup
        },
        data() {
            return {
                ready:true,
                course_id:31,
                user_id:0,
                
                
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
