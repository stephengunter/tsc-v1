
@extends('layouts.master')

@section('content')
    
    <teacher-import 
    @canceled="onCanceled" @imported="onImported">
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
            onImported(isGroup){
               
                this.backToIndex(isGroup)
            },            
            backToIndex(isGroup){
                let url='/teachers'
                if(Helper.isTrue(isGroup))  url += '?group=true'
                Helper.redirect(url)
            }

        },
    

    })
  </script>


@endsection