@extends('layouts.master')


@section('content')

       <notice-index v-show="!selected" :hide_create="indexSettings.hide_create" :version="version"
           @selected="onSelected"  @begin-create="onBeginCreate">
       </notice-index> 
      {{--  <notice-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
        @btn-back-clicked="backToIndex" @notice-deleted="onNoticeDeleted">
       </notice-details> --}}
       
@endsection



@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               version:0,
              
               selected:0,
               creating:false,
               
               indexSettings:{
                  hide_create:true, 
               },
               detailsSettings:{
                  can_back:true
               },
            }
        },
        computed: {
            indexMode() {
                 if(this.selected) return false
                  if(this.creating) return false
                  return true
            }
        },
        beforeMount() {
             
        },
        methods: {
            init(){
             
            },
            
            onSelected(id){
               this.selected=id
            },
            onNoticeDeleted(){
                this.backToIndex()
            },
            onBeginCreate(){
                 Helper.redirect('/notices/create') 
            },
            backToIndex(){
                this.version+=1
                this.selected=0
                 
            }
            

        },
    

    })
  </script>


@endsection

