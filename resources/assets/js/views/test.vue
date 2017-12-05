<template>
    <div>
        <create-bill :user_id="user_id">
           
        </create-bill>
        
    </div>
</template>

<script>
    import CreateBill from '../components/bill/create.vue'
   
    export default {
        name:'Test',
        components: {
           'create-bill':CreateBill
        },
        data() {
            return {
                ready:true,
                course_id:31,
                user_id:1,
                
                
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
