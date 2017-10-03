@extends('layouts.master')


@section('content')

       <center-index 
         @selected="onSelected"  @begin-create="onBeginCreate">
       </center-index> 

      

       
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
            
            onSelected(id){
               Helper.redirect('/centers/' + id) 
            },
            onBeginCreate(){
                 Helper.redirect('/centers/create') 
            },
           
            

        },
    

    })
  </script>


@endsection

