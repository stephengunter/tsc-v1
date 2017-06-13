@extends('layouts.master')

@section('content')

    <center-details :id="id" :can_back="can_back" 
      @btn-back-clicked="backToIndex" @center-deleted="onCenterDeleted">           
    </center-details>

          

   

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
            onCenterDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/centers')
            }

        },
    

    })
  </script>


@endsection