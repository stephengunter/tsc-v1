@extends('layouts.master')

@section('content')
  
   <category-index v-show="indexMode"  :hide_create="indexSettings.hide_create" :version="version"
    @selected="onCategorySelected" @begin-create="beginCreate">
   </category-index> 

   <category-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
     @btn-back-clicked="backToIndex" @category-deleted="onCategoryDeleted">
   </category-details>

   <category-create v-if="creating"   :course_id="course_id"
    @canceled="createCanceled" @saved="categoryCreated">
      
   </category-create>

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               version:0,

               selected:0,
               creating:false,

               course_id:0,

               indexSettings:{
                  hide_create:false
               },
               detailsSettings:{
                  can_back:true
               },

               
            }
        },
        computed: {
            indexMode() {
                 if(this.selected) return false
                  if(this.creating) return false
                  return true
            }
        },
        beforeMount() {
            
        },
        methods: {
            onCategorySelected(id){
               this.selected=id
               this.creating=false
            },
            beginCreate(course_id){
               this.selected=0
               this.course_id=course_id
               this.creating=true
            },
            onCategorySaved(category){
                this.backToIndex()
            },
            onCategoryDeleted(){
                this.backToIndex()
            },
            createCanceled(){
               this.backToIndex()
            },
            categoryCreated(category){
               this.backToIndex()
            },
            backToIndex(){
                 this.selected=0
                 this.creating=false
                 this.version +=1
            }

        },
    

    })
  </script>


@endsection