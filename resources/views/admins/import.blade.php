@extends('layouts.master')

@section('content')
   
    <admin-import 
    @canceled="onCanceled" @imported="onImported">
    </admin-import>

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
            onCanceled(){
               this.backToIndex()
            },
            onImported(){
               this.backToIndex()
            },            
            backToIndex(){
                Helper.redirect('/admins')
            }

        },
    

    })
  </script>


@endsection