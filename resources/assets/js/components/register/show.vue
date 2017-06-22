<template>
<div>
  <student-list v-if="course_id > 0" v-show="hasData" :course_id="course_id" 
     :can_edit="canEdit" @edit="onEdit"
     @loaded="onDataLoaded" @selected="onSelected">
       
  </student-list>
  <div v-show="!hasData"   class="panel panel-default">
      
      <div v-if="course" class="panel-heading">
          <div class="panel-title">
              <h4 v-html="title"></h4>
          </div>
        
          <div>
              <button v-if="course.canCreateStudent"  v-show="can_edit" @click="btnCreateClicked" class="btn btn-primary btn-sm" >
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
        name: 'ShowRegister',
        components: {
            'student-list':List,
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
               title:Helper.getIcon(Register.title())  + '  註冊學生名單', 

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
                if(!this.course.register) return false
                return true
            },
            canEdit(){
               if(this.hasData){
                   return this.course.register.canEdit
               }else return false
            }
        },
        watch:{
          'course_id' : 'init',
          'version' : 'init'
        },
        watch: {
            course_id(){
               this.init()
            },
            
        },
        beforeMount(){
           this.init()
        },
        methods: {
            init(){
              this.loaded=false
              this.register=null
            },
            onDataLoaded(data){
                this.course=data.course
            },
            onSelected(signup_id){
                this.$emit('selected',signup_id)
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
            onEdit(){
                this.$emit('begin-edit')
            }
          
        }, 
    }
</script>
