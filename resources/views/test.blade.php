@extends('layouts.master')


@section('content')
    <test></test>
      <!-- <h1>匯入教學進度</h1>   

      <form action="/test-import-schedules" method="POST" enctype="multipart/form-data">
          <input name="schedules_file" type="file">


          <button type="submit">submit</button>
      </form>    -->


      <!-- <h1>匯入上課時間</h1>   

      <form action="/test-import-classtimes" method="POST" enctype="multipart/form-data">
          <input name="classtimes_file" type="file">


          <button type="submit">submit</button>
      </form>   -->
 
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