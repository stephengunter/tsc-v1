<template>
     <div class="panel panel-default">
        <div class="panel-heading">
            <span  class="panel-title">
                 <span  class="panel-title">
                    <h4 v-html="title">
                    </h4>
                  
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
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th v-for="item in thead" v-if="item.default" v-bind:style="{ width: item.width }" >
                            {{item.title}}
                        </th>
                       
                    </tr>
                </thead>
                <tbody>
                    <row v-for="course in courses" :course="course" 
                      :more="viewMore" :select="canSelect"
                       @selected="onRowSelected" @unselected="courseUnselected">
                        
                    </row>            
                </tbody>
            </table>
           
        </div>
       
    </div>
</template>



<script>
    import Row from '../../components/course/row.vue'
    export default {
        name: 'CourseSelector',
        props: {
            courses: {
              type: Array,
              default: null
            },
            
        },
        components: {
             Row
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.courses.length) return true
                return false    
            },
            hasSelected(){
                if(this.selectedIds.length) return true
                return false    
            }
        }, 
        data() {
            return {
                title:Helper.getIcon(Course.title()) + '  請選擇課程' ,
                loaded:false,
                
                canSelect:true,
                thead:Course.getThead(true),
                viewMore:false,               
                selectedIds:[],
             
            }
        },
        methods: {
            init(){
                
                this.loaded=false  
                this.viewMore=false
                this.selectedIds=[]
            },
            
            btnViewMoreClicked(){
                this.viewMore=!this.viewMore
            },
           
            onRowSelected(id){
                this.selectedIds.push(id)
            },
            courseUnselected(id){
                Helper.removeItem(this.selectedIds , id)
            },
            submitCourses(){
                this.$emit('submit-courses',this.selectedIds);               
            }
        }
     }
</script>