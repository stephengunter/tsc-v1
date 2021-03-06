@extends('layouts.master')

@section('content')

   <notice-details   :id="id" :can_back="can_back" 
      @btn-back-clicked="backToIndex"
      @notice-deleted="onNoticeDeleted"   >
    
     
   </notice-details>

   

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
            onNoticeDeleted(){
                this.backToIndex()
            },
            backToIndex(){
               Helper.redirect('/notices')
            },
            
            // onSignupSelected(id){
            //    let url='/signups/' + id
            //    Helper.newWindow(url)
            // }

        },
    

    })
  </script>


@endsection