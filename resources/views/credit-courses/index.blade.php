@extends('layouts.master')


@section('content')
        
       <course-index v-show="indexMode" :hide_create="indexSettings.hide_create" 
           :version="version"
         @begin-create="onBeginCreate"  @details="onSelected"  >
       </course-index> 

       

       <course-details v-if="detailsMode"  :id="selected" :can_back="detailsSettings.can_back" 
          @btn-back-clicked="backToIndex" @course-deleted="onDeleted"
          @signup-selected="onSignupSelected" 
          @edit-user="onEditUser" >
       </course-details>

       <course-create v-if="creating"  :parent="createSettings.parent"
         @canceled="backToIndex" 
         @saved="onCreated" @imported="backToIndex">
        </course-create>
      
       
@endsection



@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               version:0,
              
               selected:0,
               
               indexSettings:{
                  hide_create:false, 
               },
               detailsSettings:{
                  can_back:true
               },
               createSettings:{
                  parent:0,
               },

               creating:false
            }
        },
        computed: {
            indexMode() {
                 if(this.selected) return false
                 return !this.creating
            },
            detailsMode() {
                 if(!this.selected) return false
                   return !this.creating
            },

        },
        beforeMount() {
             
        },
        methods: {
            init(){
             
            },
            onBeginCreate(parent){
              this.createSettings.parent=parent

              this.selected=0
              this.creating=true
            },        
            onSelected(id){
               this.selected=id
            },
            onDeleted(){
                this.backToIndex()
            },
            onCreated(course){
                this.creating=false     
                this.selected=course.id
            },
            backToIndex(){
                this.version+=1
                this.selected=0   
                this.creating=false              
            },
            onSignupSelected(id){
               let url='/signups/' + id
               Helper.newWindow(url)
            },
            onEditUser(user_id){
               Helper.newWindow('/users/' + user_id)
            }
           
            

        },
    

    })
  </script>


@endsection

