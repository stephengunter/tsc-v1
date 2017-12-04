@extends('layouts.master')

@section('content')
                  
  <signup-create :user_id="user_id" :course_id="course_id" @saved="onSaved"></signup-create> 

 
@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               user_id:0,
               course_id:0
            }
        },
        beforeMount() {
            @if(isset($user_id))
              this.user_id= {{ $user_id }}

            @endif

            @if(isset($course_id))
              this.course_id= {{ $course_id }}

            @endif
        },
        methods: {
            init(){

            },
            onSaved(signup){
                Helper.redirect('/signups/' + signup.id)
            }
           

        },
    

    })
  </script>


@endsection