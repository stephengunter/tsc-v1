@extends('layouts.master')

@section('content')

   <signup-details   :id="id" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @signup-deleted="onSignupDeleted"
     @print-invoice="onPrintInvoice">
  </signup-details>

   

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
            onSignupDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/signups')
            },
            onPrintInvoice(id){
               let url='/signups/' + id + '/print'
               Helper.newWindow(url)
            }

        },
    

    })
  </script>


@endsection