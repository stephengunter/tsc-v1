<template>
<div>
  <category v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="categoryUpdated" @loaded="onDataLoaded" :version="current_version"
     @btn-back-clicked="onBtnBackClicked" @deleted="onCategoryDeleted" > 

  </category>

  <category-courses v-if="loaded" :category="category"></category-courses>



  

  



  


</div>
</template>

<script>
    import Category from '../../components/category/category.vue'
    import CategoryCourses from '../../components/category/courses.vue'
    
    
    export default {
        name: 'CategoryDetails',
        components: {
            Category,
           'category-courses':CategoryCourses
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
               category:null,
               current_version:0,

               removeCourses:[],
               addCourses:[],

               courseListSettings:{
                  title:Helper.getIcon(Course.title())  + '  此分類中的課程',
                  hide_create:false,
                  can_select:true,
                  params:{
                    category:this.id,
                  },
               }
            }
        },
        computed:{
           hasAddCourses(){
               return this.addCourses.length > 0
           },
           hasRemoveCourses(){
               return this.removeCourses.length > 0
           },
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
            onCourseSelected(){

            },
            onAddCourse(){
              
            },
            onRemoveCourse(){

            }
        }, 

    }
</script>