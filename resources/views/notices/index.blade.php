@extends('layouts.master')

@section('content')
  
   <notice-index v-show="indexMode" 
    :hide_create="indexSettings.hide_create" :version="version"
    @selected="onNoticeSelected" @begin-create="beginCreate">
   </notice-index> 

   <notice-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
     @btn-back-clicked="backToIndex" @notice-deleted="onNoticeDeleted" >
  </notice-details>

   <notice-create v-if="creating"   
    @canceled="createCanceled" @saved="noticeCreated" >
      
   </notice-create>

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

               course_id:0,

               indexSettings:{
                  hide_create:false
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
            onNoticeSelected(id){
               this.selected=id
               this.creating=false
            },
            beginCreate(){
               this.selected=0
               this.creating=true
            },
            onNoticeSaved(notice){
                this.backToIndex()
            },
            onNoticeDeleted(){
                this.backToIndex()
            },
            createCanceled(){
               this.backToIndex()
            },
            noticeCreated(notice){
               this.backToIndex()
            },
            backToIndex(){
                 this.selected=0
                 this.creating=false
                 this.version +=1
            },

        },
    

    })
  </script>


@endsection