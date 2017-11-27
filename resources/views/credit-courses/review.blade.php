@extends('layouts.master')

@section('content')

    @if(isset($centerOptions))
        <course-review :version="version" @saved="onSaved" 
            :center_options="{{ json_encode($centerOptions) }}"  @saved="onSaved">   
        </course-review>

    @endif
    

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
            
            onSaved(){
             
               this.version+=1
            },            
            

        },
    

    })
  </script>


@endsection