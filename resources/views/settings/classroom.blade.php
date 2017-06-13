@extends('layouts.master')


@section('content')


    <classroom-index></classroom-index>
       
       
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

