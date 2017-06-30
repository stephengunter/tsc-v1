@extends('layouts.master')


@section('content')
        
       <course-statuses  
           :version="version"
           @selected="onSelected"  >
       </course-statuses> 
      
      
       
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
         
            onSelected(id){
               let url='/courses/' + id
               Helper.newWindow(url)
            },
           
           
            

        },
    

    })
  </script>


@endsection

