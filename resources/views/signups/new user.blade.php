@extends('layouts.master')

@section('content')
                  
  <new-user-signup @saved="onSaved"></new-user-signup> 

 
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
            onSaved(signup){
                Helper.redirect('/signups/' + signup.id)
            }
           

        },
    

    })
  </script>


@endsection