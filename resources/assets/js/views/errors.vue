<template>
    <div class="alert alert-dismissable alert-danger" >
        <h3 class="text-danger"><span class="glyphicon glyphicon-remove-circle"></span> {{ title }}</h3>
    </div>
</template>


<script>
    
     export default {
        name: 'Errors',
       

        data() {
            return {
               title:'系統無回應，請稍後再試',
               msg:''
                
            }
        },
        beforeMount(){
            this.init()
        },
        methods:{
            init(){
                let title= this.$route.params.title
                let msg= this.$route.params.msg
                if(title) this.title=title
                if(msg) this.msg=msg
            }
            
            
        }
        
    }
</script>