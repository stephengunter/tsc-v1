<template>
<div>
  <category v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="categoryUpdated" @loaded="onDataLoaded" :version="current_version"
     @btn-back-clicked="onBtnBackClicked" @deleted="onCategoryDeleted" > 

  </category>

  



  


</div>
</template>

<script>
    import Category from '../../components/category/category.vue'
    
    
    export default {
        name: 'CategoryDetails',
        components: {
            Category,
         
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            version: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            can_back:{
               type: Boolean,
               default: true
            },
        },
        data() {
            return {
               loaded:false,
               readonly:true,
               category:null,
               current_version:0,

               activeIndex:0,

               refundSettings:{
                  can_back:false
               },
               backTuitionSettings:{
                  hide_create:false
               }
            }
        },
        computed:{
           hasRefundRecord(){
              if(!this.category) return false
              if(!this.category.hasRefund) return false
                  return true
           }
        },
        beforeMount(){
           this.init()
        },
        methods: {
            init(){
              this.loaded=false
              this.readonly=true
              this.activeIndex=0
            },
            toBoolean(val){
               return val=='true'
            },
            onDataLoaded(category){
                this.loaded=true
                this.category=category
            },
            btnEditClicked(){    
              this.beginEdit()
            },
            beginEdit(){
               this.readonly=false
            },
            editCanceled(){
               this.readonly=true
            },
            categoryUpdated(){
               this.init()
            },
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onCategoryDeleted(){
               this.$emit('category-deleted')
            },
        }, 

    }
</script>