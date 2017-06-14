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
            onSaved(user){
               let url='/courses/' + user.id
               Helper.redirect(url)
            },            
            backToIndex(){
                Helper.redirect('/courses')
            }

        },
    

    })
  </script>


@endsection