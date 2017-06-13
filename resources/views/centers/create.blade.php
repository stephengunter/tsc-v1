@extends('layouts.master')

@section('content')

    <center-create @canceled="onCanceled" @saved="onSaved">
    </center-create>

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
              

               
            }
        },
        methods: {
            onCanceled(){
               this.backToIndex()
            },
            onSaved(center){
               let url='/centers/' + center.id
               Helper.redirect(url)
            },            
            backToIndex(){
                Helper.redirect('/centers')
            }

        },
    

    })
  </script>


@endsection