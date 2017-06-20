<template>
<div>
    <div v-if="category" class="panel panel-default" >
        <div class="panel-heading">
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>    
            <div>
               <select  v-model="center" @change="fetchData"  class="form-control">
                  <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
               </select>
            </div>
            <div v-if="category.canEdit">
                
                <button @click="onAddCourse" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-plus"></span> 新增
                </button>
               
            </div>
        </div>  <!-- End panel-heading--> 
        <div class="panel-body">
            <table v-if="loaded" class="table table-striped">
                <thead>
                    <tr>
                       <th style="width:5%"></th>
                       <th>開課中心</th>
                       <th>課程名稱</th>
                       <th>課程編號</th>
                       <th>教師</th>
                       <th>課程日期</th>
                       <th>報名日期</th>
                    </tr>
                </thead>
                <tbody>
                   <tr v-for="course in courseList">
                      <td>
                          <button class="btn btn-danger btn-xs"
                            @click.prevent="btnDeleteClicked(course.id,course.name)">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                          </button>
                         
                      </td>
                      <td v-text="course.center.name"></td>
                      <td v-text="course.name"></td>
                      <td v-text="course.number"></td>
                      <td v-html="teachersText(course.teachers)"></td>
                      <td v-html="period(course.begin_date , course.end_date)"></td>
                      <td v-html="period(course.open_date , course.close_date)"></td>
                   </tr>
                </tbody>
            </table>
        </div> <!-- End panel-body--> 
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
            version:{
              type: Number,
              default: 0
            }
            
        },
        data() {
            return {
               title:Helper.getIcon(Course.title())  + '  此分類中的課程',
               loaded:false,

               center:0,
              
               centerOptions:[],
               courseList:[],


               removeCourses:[],
               addCourses:[],

               
            }
        },
        watch: {
            version: function (value) {
               this.fetchData()
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
               let options=Center.options()
                options.then(data=>{
                    this.centerOptions=data.options
                    let allCenters={ text:'全部開課中心' , value:'0' }
                    this.centerOptions.splice(0, 0, allCenters);
                    this.center=this.centerOptions[0].value

                    this.fetchData()
                }).catch(error =>{
                    Helper.BusEmitError(error)
                })
            },
            fetchData(){
                let category=this.category.id
                let center=this.center
                let getData=CategoryCourses.index(category,center)
                getData.then(data=>{
                   this.courseList=data.courseList
                   this.loaded=true
                }).catch(error=>{
                   Helper.BusEmitError(error)
                   this.loaded=false
                })
            },
            teachersText(teachers){
                 return Course.teachersText(teachers)             
            },
            period(begin,end){
               return Helper.period(begin,end)
            },
            remove(id){
               this.removeCourses.push(id)
            },
            unRemove(id){
               Helper.removeItem(this.removeCourses , id)
            },
            onAddCourse(){
               this.$emit('add-course')
            },
            btnDeleteClicked(id, name){

               let values={
                   id:id,
                   name:name
               }
               this.$emit('remove',values)            
         
            }
        }, 

    }
</script>