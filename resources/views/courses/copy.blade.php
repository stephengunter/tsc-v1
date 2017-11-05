
@extends('layouts.master')

@section('content')
     
    
    
        <course-copy   :term_options="{{ json_encode($termOptions) }}"
            :center_options="{{ json_encode($centerOptions) }}"
            @canceled="onCanceled" @imported="onImported" >   
        </course-copy >

   

  
    

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