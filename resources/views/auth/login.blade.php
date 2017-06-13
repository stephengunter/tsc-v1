@extends('layouts.master')

@section('content')
                    
  <login @logined="onLogined" @forgot-password="onForgotPassword"></login>

  
                           
                           
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
            init(){
             
            },
            onForgotPassword(){
               let url='/forgot-password'
               Helper.redirect(url)
            },
            onLogined(){
               let url='/'
               Helper.redirect(url)
            }

        },
    

    })
  </script>


@endsection