@extends('layouts.master')

@section('content')
   
    <category-import 
    @canceled="onCanceled" @imported="onImported">
    </category-import>

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
                Helper.redirect('/categories')
            }

        },
    

    })
  </script>


@endsection