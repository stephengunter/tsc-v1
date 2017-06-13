<template>
    <div> 
        <edit-category  @endEditCategory="backToIndex" @saved="backToIndex"  ></edit-category>
        
    </div>
</template>


<script>
   
     import EditCategory from '../../components/category/edit-category.vue'

    export default {
        components:{
            EditCategory
        },
        name:'CreateCategory',
        data()  {
            return {    
               
                categoryLoaded:false,
            }
        },
        beforeMount(){
            this.init()
        },
        methods:{
            init(){
                this.isReadOnly=false
            },
            backToIndex(){    
                this.$router.push('/categories');
            },           
           

        }

    }
</script>