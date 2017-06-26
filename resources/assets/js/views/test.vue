<template>
     <div>
         <leave-list :lesson_id="lesson_id"></leave-list>
     </div>
</template>

<script>
    import LeaveList from '../components/lesson/leave/list.vue'
    

    export default {
        components: {
            'leave-list':LeaveList,
        },
        data() {
            return {
                loaded:false,
                lesson_id:1,
                keys:[],
                test:'',
            }
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
              
            },
            fetchData() {
                
            }, 
            
            
            
            
        }
    }
</script>
