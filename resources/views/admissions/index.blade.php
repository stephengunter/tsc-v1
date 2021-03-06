@extends('layouts.master')

@section('content')
  
   <admission-index 
    :hide_create="indexSettings.hide_create" :version="version"
    @signup-selected="onSignupSelected" >
    
   </admission-index> 

   
@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               version:0,

               indexSettings:{
                  hide_create:false
               },

               
            }
        },
        beforeMount() {
            
        },
        methods: {
            onSignupSelected(signup_id){
                Helper.newWindow('/signups/' + signup_id)
            },

        },
    

    })
  </script>


@endsection