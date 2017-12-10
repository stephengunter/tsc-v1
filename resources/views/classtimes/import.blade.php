
@extends('layouts.master')

@section('content')
     
    
    <classtimes-import  :center_options="{{ json_encode($centerOptions) }}"   @imported="onImported">
         
    </classtimes-import>

  
    

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
                let url='/courses'
                Helper.redirect(url)
            }

        },
    

    })
  </script>


@endsection