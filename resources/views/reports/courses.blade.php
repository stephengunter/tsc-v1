@extends('layouts.master')


@section('content')
        
       <courses-report></courses-report> 
         
          
       
      
      
       
@endsection



@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               version:0,
              
               selected:0,
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

