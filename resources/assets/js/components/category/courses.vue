<template>
<div>
  

  <div v-if="category" class="panel panel-default show-data">
      <div class="panel-heading">
          <span class="panel-title">
              <h4 v-html="title"></h4>
          </span>    
          <div v-if="category.canEdit">
              
              <button @click="onAddCourse" class="btn btn-primary btn-sm" >
                  <span class="glyphicon glyphicon-pencil"></span> 新增
              </button>
              <button disabled="!hasAddCourses" @click="onRemoveCourse" class="btn btn-danger btn-sm" >
                  <span class="glyphicon glyphicon-trash"></span> 移除
              </button>
          </div>
      </div>  <!-- End panel-heading--> 

  </div>



  

  



  


</div>
</template>

<script>
    export default {
        name: 'CategoryCourses',
        props: {
            category: {
              type: Object,
              default: null
            },
            
        },
        data() {
            return {
               title:Helper.getIcon(Course.title())  + '  此分類中的課程',
               loaded:false,
              
               courseList:[],
               removeCourses:[],
               addCourses:[],

               
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
               this.fetchData()
            },
            fetchData(){
                let  category_id=this.category.id
                let getData=Category.activeCourses(category_id)
                getData.then(data=>{
                   this.courseList=data.courseList
                   this.loaded=true
                }).catch(error=>{

                })
            },
            
            onAddCourse(){
              
            },
            onRemoveCourse(){

            }
        }, 

    }
</script>