@extends('layouts.master')

@section('content')

    <teacher-create :user_id="userId"
    @canceled="onCanceled" @saved="onSaved">
    </teacher-create>

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
            onSaved(teacher){
               let url='/teachers/' + teacher.user_id
               Helper.redirect(url)
            },            
            backToIndex(){
                Helper.redirect('/teachers')
            }

        },
    

    })
  </script>


@endsection