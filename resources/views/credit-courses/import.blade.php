
@extends('layouts.master')

@section('content')
     
    
    <credit-course-import :type_options="{{ json_encode($typeOptions) }}"  
        @canceled="onCanceled" @imported="onImported">
    </credit-course-import>

  
    

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
                let url='/credit-courses'
                Helper.redirect(url)
            }

        },
    

    })
  </script>


@endsection