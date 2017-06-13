@extends('layouts.master')


@section('content')


    <term-index></term-index>
       
       
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

