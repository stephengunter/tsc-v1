@extends('layouts.master')

@section('content')
    
    <teacher-review :version="version"  @saved="onSaved">   
    </teacher-review>
    
    <!-- <teacher-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
        @btn-back-clicked="backToIndex" @teacher-deleted="onTeacherDeleted">
    </teacher-details> -->

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
                version:0
            }
        },
        beforeMount() {
            
        },
        methods: {
            onCanceled(){
               this.backToIndex()
            },
            onSaved(){
               this.version+=1
            },            
            backToIndex(){
                Helper.redirect('/teachers')
            }

        },
    

    })
  </script>


@endsection