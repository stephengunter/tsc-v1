
@extends('layouts.master')

@section('content')
    
    <volunteer-import 
    @canceled="onCanceled" @imported="onImported">
    </volunteer-import>

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
                let url='/volunteers'
                Helper.redirect(url)
            }

        },
    

    })
  </script>


@endsection