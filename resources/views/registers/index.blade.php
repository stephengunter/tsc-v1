@extends('layouts.master')

@section('content')
  
   <register-index 
    :hide_create="indexSettings.hide_create" :version="version"
    @signup-selected="onSignupSelected" 
    @edit-user="onEditUser"
    >
    
   </register-index> 

   
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
            onEditUser(user_id){
               Helper.newWindow('/users/' + user_id)
            }

        },
    

    })
  </script>


@endsection