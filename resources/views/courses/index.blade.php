@extends('layouts.master')


@section('content')
        
       <course-index v-show="!selected" :hide_create="indexSettings.hide_create" 
           :version="version"
         @begin-create="onBeginCreate"  @selected="onSelected"  >
       </course-index> 

       

       <course-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
          @btn-back-clicked="backToIndex" @course-deleted="onDeleted"
          @signup-selected="onSignupSelected" 
          @edit-user="onEditUser" >
       </course-details>
      
       
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
                  hide_create:true, 
               },
               detailsSettings:{
                  can_back:true
               },
            }
        },
        computed: {
            indexMode() {
                 if(this.selected) return false
                  return true
            }
        },
        beforeMount() {
             
        },
        methods: {
            init(){
             
            },
            onBeginCreate(){
                Helper.redirect('/courses/create')
            },        
            onSelected(id){
               this.selected=id
            },
            onDeleted(){
                this.backToIndex()
            },
            backToIndex(){
                this.version+=1
                this.selected=0                 
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

