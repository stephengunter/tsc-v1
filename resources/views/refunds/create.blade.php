@extends('layouts.master')

@section('content')
  
   
   <refund-create :signup_id="signup_id" @back="backToIndex" @saved="backToIndex">
   </refund-create>
    
      
    

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
                signup_id:0
            }
        },
        beforeMount() {
            @if(isset($signup_id))
               this.signup_id= {{ $signup_id }}

            @endif
        },
        methods: {
            backToIndex(){
               Helper.redirect('/refunds')
            }

        },
    

    })
  </script>


@endsection