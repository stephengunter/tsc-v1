@extends('layouts.master')

@section('content')
                  
  <new-user-signup ></new-user-signup> 

 
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