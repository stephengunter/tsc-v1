<template>
     <div class="panel panel-default">
        <div class="panel-heading">
            <span  class="panel-title">
                 <span  class="panel-title">
                    <h4 v-html="title">
                    </h4>
                  
                  </span>
            </span>
           
            
        </div>
        <div class="panel-body" v-if="hasData">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th> 課程名稱 </th>
                        <th> 學分數 </th>
                        <th> 學費 </th>
                        <th> 必修/選修 </th>
                        <th> 人數上限</th>
                        <th> 報名人數 </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="course in courses" :course="course" >

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
                        <td>
                            {{ course.limit  }}
                        </td>
                         <td>
                            {{ course.validSignups  }}
                        </td>
                    </tr>            
                </tbody>
            </table>
           
        </div>
       
    </div>
</template>



<script>
    
    export default {
        name: 'SubCourseList',
        props: {
            courses: {
              type: Array,
              default: null
            },
            
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.courses.length) return true
                return false    
            },
        }, 
        data() {
            return {
                title:Helper.getIcon(Course.title()) + '  群組課程' ,
                loaded:false,
               
                viewMore:false,    
             
            }
        },
        methods: {
            init(){
                this.loaded=false 
            },
            isMust(must){
                must=Helper.isTrue(must)
                if(must) return ' <span class="label label-info"> 必修 </span>'
                return ' <span class="label label-default"> 選修 </span>'
            },

              
        }
     }
</script>