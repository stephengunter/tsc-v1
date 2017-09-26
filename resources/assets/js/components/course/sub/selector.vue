<template>
     <div class="panel panel-default">
        <div class="panel-heading">
            <span  class="panel-title">
                 <span  class="panel-title">
                    <h4 v-html="title">
                    </h4>
                  
                  </span>
            </span>
           
            <div v-if="show_submit"  v-show="can_select" class="form-inline">  
                <button type="submit" @click="submitCourses" class="btn btn-success" :disabled="!hasSelected">確認送出</button>
            </div>
        </div>
        <div class="panel-body" v-if="hasData">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th v-if="can_select"> &nbsp; </th>
                        <th> 課程名稱 </th>
                        <th> 學分數 </th>
                        <th> 學費 </th>
                        <th> 必修/選修 </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="course in courses" :course="course" >

                       <td v-if="can_select">
                            <checkbox :default="beenSelected(course.id)"
                              @selected="selected(course.id)"
                              @unselected="unselected(course.id)" >
                            </checkbox>
                       </td>
                        <td>
                            {{ course.number }}  &nbsp;
                            {{ course.name }}
                        </td>
                        <td>
                            {{ course.credit_count  }}
                        </td>
                        <td>
                            {{ course.tuition | formatMoney  }}
                        </td>
                        <td v-html="isMust(course.must)">

                           
                        </td>
                        
                    </tr>            
                </tbody>
            </table>
           
        </div>
       
    </div>
</template>



<script>
    
    export default {
        name: 'SubCourseSelector',
        props: {
            courses: {
              type: Array,
              default: null
            },
            can_select:{
                type: Boolean,
                default: true
            },
            default_selected:{
                type: Array,
                default: null
            },
            show_submit: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            },
            
        },
        watch: {
            version () {
               this.submitCourses()
            }
           
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
                viewMore:false,               
                selectedIds:[],
             
            }
        },
        methods: {
            init(){
                this.loaded=false  
                this.selectedIds=[]
                
                if(this.can_select){

                    this.title=Helper.getIcon(Course.title()) + '  請選擇課程'
                    if(this.default_selected){
                        
                        for(let i=0; i<this.default_selected.length;i++){
                            this.selectedIds.push(this.default_selected[i])
                        }
                     }


                }else{
                    this.title=Helper.getIcon(Course.title()) + '  報名課程'

                }

               


                

               
            },
            beenSelected(id){
                return this.selectedIds.includes(id) 
            },
            submitCourses(){
                this.$emit('submit-courses',this.selectedIds)           
            },
            selected(id){
                this.selectedIds.push(id)       
            },
            isMust(must){
                must=Helper.isTrue(must)
                if(must) return ' <span class="label label-info"> 必修 </span>'
                return ' <span class="label label-default"> 選修 </span>'
            },
            unselected(id){
                Helper.removeItem(this.selectedIds , id)          
            },

              
        }
     }
</script>