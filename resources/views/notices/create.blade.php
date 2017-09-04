@extends('layouts.master')

@section('content')
  
   
   <notice-create @canceled="backToIndex" @saved="backToIndex"></notice-create>
    
      
    

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               
            }
        },
        beforeMount() {
            
        },
        methods: {
            backToIndex(){
               Helper.redirect('/notices')
            }

        },
    

    })
  </script>


@endsection