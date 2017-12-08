<template>
   <data-viewer v-if="loaded"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="searchParams"  :thead="thead"  
      :filter="filter"  :title="title" :create_text="createText" :page_size="page_size"
      @refresh="init" :version="version"    @dataLoaded="onDataLoaded">
       
      <div  class="form-inline" slot="header">
         <span v-if="summary" >
               總數：{{ summary.total }} 人 &nbsp; 已退出：{{ summary.canceled }} 人 
               &nbsp;&nbsp;&nbsp;&nbsp;
         </span>
        
               
      </div>
      <template scope="props">
         <row  :student="props.item">
               
              
               
            
         </row>
      </template>
               
       
       

   </data-viewer>

</template>

<script>
   import Row from './row.vue'  
   export default {
      name: 'SignupList',
      components: {
            Row
      },
      props: {
         course_id: {
            type: Number,
            default: 0
         },
      },
      beforeMount() {
         
         this.init()
      },
      watch: {
         course_id (value) {
            this.searchParams.course=value
         }
      },
      data() {
         return {
               version:0,
               title:Helper.getIcon(Student.title())  + '  學員管理',
               loaded:false,
               source: Student.source(),
               page_size:50,
               defaultSearch:'user.profile.fullname',
               defaultOrder:'',                
               createText:'',
               
               thead:[],
               filter: [{
                  title: '姓名',
                  key: 'user.profile.fullname',
               }],
            
               searchParams:{
                  course : 0
               },
              
               summary:{
                  total:0,
                  canceled : 0
               },


            
         }
      },
      computed: {
         
         
      },
      methods: {
         init() {
            this.thead=Student.getThead()
            this.searchParams.course=this.course_id
            this.loaded=true
               
               
         },
         onDataLoaded(data){
             this.summary=data.summary
           
         }, 
         
         
         
      },

   }
</script>