@extends('layouts.master')

@section('content')

    <course-create @canceled="onCanceled" @saved="onSaved">
    </course-create>

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
              

               
            }
        },
        methods: {
            onCanceled(){
               this.backToIndex()
            },
            onSaved(course){
               let url='/courses/' + course.id
               Helper.redirect(url)
            },            
            backToIndex(){
                Helper.redirect('/courses')
            }

        },
    

    })
  </script>


@endsection