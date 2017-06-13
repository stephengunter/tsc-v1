@extends('layouts.master')

@section('content')

   <teacher-details   :id="id" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @teacher-deleted="onTeacherDeleted">
  </teacher-details>

   

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               id:0,
               can_back:true
            }
        },
        beforeMount() {
            @if(isset($id))
              this.id= {{ $id }}

            @endif
        },
        methods: {
            onTeacherDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/teachers')
            }

        },
    

    })
  </script>


@endsection