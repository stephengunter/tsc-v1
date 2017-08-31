@extends('layouts.master')

@section('content')
  
   <score-index 
    :hide_create="indexSettings.hide_create"  >
    
   </score-index> 

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               version:0,

              

               indexSettings:{
                  hide_create:true
               },
             
               
            }
        },
       
        beforeMount() {
            
        },
        methods: {
            

        },
    

    })
  </script>


@endsection