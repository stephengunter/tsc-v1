@extends('layouts.master')

@section('content')

  <user-details :id="id" :can_back="can_back" 
      @btn-back-clicked="backToIndex" @user-deleted="onUserDeleted">           
   </user-details>

          

   

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               id:0,
               can_back:true
            }
        },
        beforeMount() {
            @if(isset($id))
              this.id= {{ $id }}

            @endif
        },
        methods: {
            onUserDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/users')
            }

        },
    

    })
  </script>


@endsection