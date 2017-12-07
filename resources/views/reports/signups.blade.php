@extends('layouts.master')


@section('content')
        
       
         
          
       <signups-report  :center_options="{{ json_encode($centerOptions) }}"
           :term_options="{{ json_encode($termOptions) }}">
       <signups-report>
      
      
       
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

