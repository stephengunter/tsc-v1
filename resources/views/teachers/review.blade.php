@extends('layouts.master')

@section('content')
    
    <teacher-review :version="version"  @saved="onSaved">   
    </teacher-review>
    
    

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
                version:0
            }
        },
        beforeMount() {
            
        },
        methods: {
            onCanceled(){
               this.backToIndex()
            },
            onSaved(){
               this.version+=1
            },            
            backToIndex(){
                Helper.redirect('/teachers')
            }

        },
    

    })
  </script>


@endsection