@extends('layouts.master')


@section('content')


    <identity-index></identity-index>
       
       
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

