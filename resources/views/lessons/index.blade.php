@extends('layouts.master')

@section('content')
  
   <lesson-index v-show="indexMode"  :hide_create="indexSettings.hide_create" :version="version"
    @selected="onLessonSelected" @begin-create="beginCreate">
   </lesson-index> 

   <lesson-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
     @btn-back-clicked="backToIndex" @lesson-deleted="onLessonDeleted">
  </lesson-details>

   <lesson-create v-if="creating"   :course_id="course_id"
    @canceled="createCanceled" @saved="lessonCreated">
      
   </lesson-create>

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
            onLessonSelected(id){
               this.selected=id
               this.creating=false
            },
            beginCreate(course_id){
               this.selected=0
               this.course_id=course_id
               this.creating=true
            },
            onLessonSaved(lesson){
                this.backToIndex()
            },
            onLessonDeleted(){
                this.backToIndex()
            },
            createCanceled(){
               this.backToIndex()
            },
            lessonCreated(lesson){
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