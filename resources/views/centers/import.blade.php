@extends('layouts.master')

@section('content')
   center-import
    <!-- <center-import 
    @canceled="onCanceled" @saved="onSaved">
    </center-import> -->

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
            onSaved(){
               this.backToIndex()
            },            
            backToIndex(){
                Helper.redirect('/centers')
            }

        },
    

    })
  </script>


@endsection