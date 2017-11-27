@extends('layouts.master')

@section('content')

   <course-details   :id="id" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @course-deleted="onCourseDeleted"
     @signup-selected="onSignupSelected" 
     @edit-user="onEditUser">
     
   </course-details>

   

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
            onCourseDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/courses')
            },
            onSignupSelected(id){
               let url='/signups/' + id
               Helper.newWindow(url)
            },
            onEditUser(user_id){
               Helper.newWindow('/users/' + user_id)
            }

        },
    

    })
  </script>


@endsection