<template>
     <div>
         <options-filter :params="params"></options-filter>
     </div>
</template>

<script>
    

    export default {
        components: {
           
        },
        data() {
            return {
                ready:false,
                params:{
                    term:0,
                    center:0,
                    category:0,
                    weekday:0,
                    parent:0,
                },
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
