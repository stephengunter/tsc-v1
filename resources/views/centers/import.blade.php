@extends('layouts.master')

@section('content')
   
    <center-import  :areas="{{ json_encode($areas) }}"  
    @canceled="onCanceled" @imported="onImported">
    </center-import>

    

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
                Helper.redirect('/centers')
            }

        },
    

    })
  </script>


@endsection