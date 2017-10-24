@extends('layouts.master')

@section('content')
    
    <teacher-import 
    @canceled="onCanceled" @saved="onSaved">
    </teacher-import>

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
               let url='/teachers' 
               Helper.redirect(url)
            },            
            backToIndex(){
                Helper.redirect('/teachers')
            }

        },
    

    })
  </script>


@endsection