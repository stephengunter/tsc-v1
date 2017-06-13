@extends('layouts.master')

@section('content')

    <user-create @canceled="onCanceled" @saved="onSaved">
    </user-create>

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
              

               
            }
        },
        methods: {
            onCanceled(){
               this.backToIndex()
            },
            onSaved(user){
               let url='/users/' + user.id
               Helper.redirect(url)
            },            
            backToIndex(){
                Helper.redirect('/users')
            }

        },
    

    })
  </script>


@endsection