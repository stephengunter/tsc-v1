@extends('layouts.master')

@section('content')

    <volunteer-create :user_id="userId"
    @canceled="onCanceled" @saved="onSaved">
    </volunteer-create>

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               userId:0
            }
        },
        beforeMount() {
            @if(isset($id))
              this.userId= {{ $id }}

            @endif
        },
        methods: {
            onCanceled(){
               this.backToIndex()
            },
            onSaved(volunteer){
               let url='/volunteers/' + volunteer.user_id
               Helper.redirect(url)
            },            
            backToIndex(){
                Helper.redirect('/volunteers')
            }

        },
    

    })
  </script>


@endsection