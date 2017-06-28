@extends('layouts.master')

@section('content')

   <lesson-details   :id="id" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @lesson-deleted="onLessonDeleted" 
     @print-lesson="onPrintLesson">
     
   </lesson-details>

   

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
            onLessonDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/lessons')
            },
            onPrintLesson(id){
                let url='/lessons/' + id + '/print'
                Helper.newWindow(url)
            }
            // onSignupSelected(id){
            //    let url='/signups/' + id
            //    Helper.newWindow(url)
            // }

        },
    

    })
  </script>


@endsection