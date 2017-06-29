@extends('layouts.master')

@section('content')

   <admin-details   :id="id" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @admin-deleted="onAdminDeleted">
   </admin-details>

   

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
            onAdminDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/admins')
            }

        },
    

    })
  </script>


@endsection