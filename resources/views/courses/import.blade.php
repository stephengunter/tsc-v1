
@extends('layouts.master')

@section('content')
     
    
    <course-import 
        @canceled="onCanceled" @imported="onImported">
    </course-import>

  
    

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
            backToIndex(isGroup){
                let url='/courses'
                Helper.redirect(url)
            }

        },
    

    })
  </script>


@endsection