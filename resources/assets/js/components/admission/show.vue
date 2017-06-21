<template>
<div>
  <admit-list v-show="hasData" :course_id="course_id" 
     @loaded="onDataLoaded">
       
  </admit-list>
  <div v-show="!hasData"  class="panel panel-default">
      <!-- <div v-if="admission" class="panel-heading">
          <div class="panel-title">
             <h4 v-html="title"></h4>
              報名數：{{ summary.total }} 筆 &nbsp; 
              正取：{{ summary.in }} 筆 &nbsp; 
              備取：{{ summary.out }} 筆 &nbsp; 
            
          </div>    
          <div>
             
          </div>
      </div>   -->
      <div v-if="course" class="panel-heading">
          <div class="panel-title">
              <h4 v-html="title"></h4>
              
            
          </div>
          <!-- <div>
              報名數：{{ summary.total }} 筆 &nbsp; 
              人數上限：{{ summary.in }} 筆 &nbsp; 
              最低人數：：{{ summary.out }} 筆 &nbsp; 
          </div> -->
          <div>
              <button v-if="course.canCreateAdmit"  v-show="can_edit" @click="btnCreateClicked" class="btn btn-primary btn-sm" >
                  <span class="glyphicon glyphicon-plus"></span> 新增
              </button>
          </div>
      </div>  <!-- End panel-heading-->   
      
  </div>  


  

</div>

  


</template>

<script>
    import List from './list.vue'

    export default {
        name: 'ShowAdmission',
        components: {
            'admit-list':List,
        },
        props: {
            course_id: {
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
               title:Helper.getIcon(Admission.title())  + '  錄取名單', 

               loaded:false,
               course:null,

               summary:{
                  total:31,
                  in:20,
                  out:11
               }
            }
        },
        computed: {
            hasData: function () {
                if(!this.course) return false
                if(!this.course.admission) return false
                return true
            }
        },
        watch:{
          'course_id' : 'init',
          'version' : 'init'
        },
        beforeMount(){
           this.init()
        },
        methods: {
            init(){
              this.loaded=false
              this.admission=null
            },
            onDataLoaded(data){
                this.course=data.course
            },
            btnCreateClicked(){
               this.$emit('begin-create') 
            },
            btnDeleteClicked(){
                 let values={
                    name: this.category.name,
                    id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
          
        }, 
    }
</script>
