<template>
<div>
   <category :id="id" :version="version"  :canEdit="canEdit" @loaded="onLoaded" @deleted="backToIndex" ></category>
  
  <course-list v-if="loaded" :category="category" :version="version" 
      @addCourse="onAddCourse" @deleted="onCourseDeleted">      
  </course-list>

  <modal :showbtn="false" :width="1200" :show.sync="showImport"  @closed="showImport=false" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="showImport=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           <h3>將課程加入分類</h3>
          </div>
        <div slot="modal-body" class="modal-body">
           
            <course-selector v-if="showImport" @submitCourses="importCourses" :params="params">
            </course-selector>
      
        </div>
  </modal>

 

</div>    
</template>
<script>
    
    import Category from '../../components/category/category.vue'
    import CourseList from '../../components/course/course-list.vue'
    import CourseSelector from '../../components/course/course-selector.vue'

    export default {
        components: {
            'checkbox':CheckBox,
            Modal,
            Category,
            'course-list':CourseList,
            'course-selector':CourseSelector
        },
        name: 'CategoryView',
        data(){
            return{
                
                loaded:false,
                id:0,
                canEdit:true,
                showImport:false,
                category:{},
              
                params:{
                    center:0,
                    term:0,
                    category:0,
                    active:1
                },
                version:0,

               
            }
        },
        beforeMount(){
           this.init()
        },
        watch: {
            '$route': 'init'
        },
        methods:{
            init(){
                this.id=this.$route.params.id
                this.category={}
                this.loaded=false
                this.showImport=false
                this.canEdit=true
                this.params={
                    center:0,
                    term:0,
                    category:this.id,
                    active:1
                }
                this.version=0

                
            },
            backToIndex() {
                this.$router.push('/categories')
            },
            onLoaded(category){
                this.category=category
                this.loaded=true
            },
            onAddCourse(center,term){
                this.params.center=center
                this.params.term=term
                this.showImport=true
            },
            onCourseDeleted(){
                this.version+=1
            },
            importCourses(selectedIds){
                 let url = '/api/category-course/import' 
                
                let form=new Form({
                    category:this.id,
                    courseIds:selectedIds
                })
                form.post(url)
                .then(result => {
                    this.showImport=false
                    this.version +=1
                    Helper.BusEmitOK('加入成功')
                })
                .catch(error => {
                    this.showImport=false
                    Helper.BusEmitError(error,'加入失敗')
                })

            },
            

           
            
        }
        
    }
</script>
