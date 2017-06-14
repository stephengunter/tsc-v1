@extends('layouts.master')

@section('content')

   <course-details   :id="id" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @course-deleted="onCourseDeleted">
   </course-details>

   

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
            onCourseDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/courses')
            }

        },
    

    })
  </script>


@endsection