<template>
<div>
  <category v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="categoryUpdated" @loaded="onDataLoaded" :version="current_version"
     @btn-back-clicked="onBtnBackClicked" @deleted="onCategoryDeleted" > 

  </category>

  <category-courses v-if="loaded" :category="category"
     :version="current_version"
     @add-course="onAddCourse" >
    
  </category-courses>

 
  
  
  <modal :showbtn="false" :width="courseSelector.width" :show.sync="courseSelector.show" 
        @closed="onAddCourseCanceled"   effect="fade">
       
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onAddCourseCanceled">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           <h3>選擇要加入此分類的課程</h3>
          </div>
        <div slot="modal-body" class="modal-body">
           
           <course-selector v-if="courseSelector.show"
             :courses="courseSelector.list"  
             @submit-courses="submitAddCourses">
                
            </course-selector>
      
        </div>
 </modal> 
  



  


</div>
</template>

<script>
    import CategoryComponent from '../../components/category/category.vue'
    import CategoryCoursesComponent from '../../components/category/courses.vue'
    import CourseSelector from '../../components/course/selector.vue'
    
    
    export default {
        name: 'CategoryDetails',
        components: {
            'category':CategoryComponent,
           'category-courses':CategoryCoursesComponent,
           'course-selector':CourseSelector
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

               courseSelector:{
                  list:[],
                  show:false,
                  width:1200,
               },
              

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
            getCoursesCanAdd(){
               let getData=CategoryCourses.create(this.id)
               getData.then(data=>{
                    this.courseSelector.list=data.courseList
                    this.courseSelector.show=true
                }).catch(error=>{
                    Helper.BusEmitError(error)
                })

            },
            onCourseSelected(){

            },
            onAddCourse(){
                this.getCoursesCanAdd()
            },
            onAddCourseCanceled(){
                this.courseSelector.show=false
            },
            onRemoveCourse(){

            },
            submitAddCourses(selectedIds){
              let category=this.id
              let courses=selectedIds
              let store=CategoryCourses.store(category,courses)
              store.then(data => {
                 this.courseSelector.show=false
                 this.current_version += 1
                 Helper.BusEmitOK()
                                          
              })
              .catch(error => {
                    Helper.BusEmitError(error) 
              })
            }
        }, 

    }
</script>