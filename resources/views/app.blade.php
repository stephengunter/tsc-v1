@extends('layouts.master')


@section('content')

      <menus></menus>          
 
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

