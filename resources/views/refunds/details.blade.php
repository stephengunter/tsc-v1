@extends('layouts.master')

@section('content')

   <refund-details   :id="id" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @refund-deleted="onRefundDeleted">
  </refund-details>

   

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
            onRefundDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/refunds')
            }

        },
    

    })
  </script>


@endsection