@extends('layouts.master')


@section('content')
   
       <course-index v-show="!selected" :hide_create="indexSettings.hide_create" :version="version"
           @selected="onSelected"  @begin-create="onBeginCreate">
       </course-index> 
      {{--  <teacher-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
        @btn-back-clicked="backToIndex" @teacher-deleted="onTeacherDeleted">
       </teacher-details> --}}
       
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
                  if(this.creating) return false
                  return true
            }
        },
        beforeMount() {
             
        },
        methods: {
            init(){
             
            },
            
            onSelected(id){
               this.selected=id
            },
            onTeacherDeleted(){
                this.backToIndex()
            },
            onBeginCreate(){
                 Helper.redirect('/teachers/create') 
            },
            backToIndex(){
                this.version+=1
                this.selected=0
                 
            }
            

        },
    

    })
  </script>


@endsection

