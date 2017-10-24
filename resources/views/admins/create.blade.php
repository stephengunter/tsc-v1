@extends('layouts.master')

@section('content')

    <admin-create :user_id="userId"
    @canceled="onCanceled" @saved="onSaved" @imported="onImported">
    </admin-create>

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
            onSaved(admin){
               let url='/admins/' + admin.user_id
               Helper.redirect(url)
            },     
            onImported(){
                
                this.backToIndex()
            },       
            backToIndex(){
                Helper.redirect('/admins')
            }

        },
    

    })
  </script>


@endsection