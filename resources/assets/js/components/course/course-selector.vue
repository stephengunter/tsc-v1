<template>
     <div class="panel panel-default">
        <div class="panel-heading">
            <span  class="panel-title">
                 <span  class="panel-title">
                    <h4><i class="fa fa-calendar-o" aria-hidden="true"></i> 請選擇課程</h4>
                  
                  </span>
            </span>
            
            <div>
                <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="btnViewMoreClicked">
                <span v-if="viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                  <span v-if="!viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
                </button>
            </div>
            <div class="form-inline">  
                <button type="submit" @click="submitCourses" class="btn btn-success" :disabled="!hasSelected">確認送出</button>
            </div>
        </div>
        <div class="panel-body" v-if="hasData">
          <course-table :courses="courseList" :more="viewMore" :select="true"
            @selected="courseSelected" @unselected="courseUnselected">
              
          </course-table>
           
        </div>
       
    </div>
</template>



<script>
    import CourseTable from '../../components/course/course-table.vue'
    export default {
        props:['params'],
        name: 'CourseSelector',
        components: {
             CourseTable
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.courseList.length) return true
                return false    
            },
            hasSelected(){
                if(this.selectedIds.length) return true
                return false    
            }
        }, 
        data() {
            return {
                loaded:false,
                
                courseList:[],

                viewMore:false,               
                selectedIds:[],
             
            }
        },
        methods: {
            init(){
                this.loaded=false  
                this.viewMore=false
                this.selectedIds=[]
                this.courseList=[]  
                this.fetchData()
            },
            fetchData(){
                let url= Helper.buildQuery('/api/category-course/courses-not-in-Category',this.params)
              
                axios.get(url)
                    .then(response => {
                       this.courseList=response.data.courseList
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            btnViewMoreClicked(){
                this.viewMore=!this.viewMore
            },
           
            courseSelected(id){
                this.selectedIds.push(id)
            },
            courseUnselected(id){
                Helper.removeItem(this.selectedIds , id)
            },
            submitCourses(){
                this.$emit('submitCourses',this.selectedIds);               
            }
        }
     }
</script>