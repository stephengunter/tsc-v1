@extends('layouts.master')

@section('content')

   <volunteer-details   :id="id" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @volunteer-deleted="onVolunteerDeleted">
   </volunteer-details>

   

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
            onVolunteerDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/volunteers')
            }

        },
    

    })
  </script>


@endsection