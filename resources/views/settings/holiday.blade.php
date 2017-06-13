@extends('layouts.master')


@section('content')


    <holiday-index></holiday-index>
       
       
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
            init(){
             
            },
            
            
            

        },
    

    })
  </script>


@endsection

