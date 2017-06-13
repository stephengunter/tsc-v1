@extends('layouts.master')

@section('content')
  
   
   <refund-create @back="backToIndex" @saved="backToIndex"></refund-create>
    
      
    

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
               Helper.redirect('/refunds')
            }

        },
    

    })
  </script>


@endsection