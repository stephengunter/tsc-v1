@extends('layouts.master')


@section('content')


    <discount-index :center_options="{{ json_encode($centerOptions) }}">
    </discount-index>
       
       
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

