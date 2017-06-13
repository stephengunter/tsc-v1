@extends('layouts.master')

@section('content')
  
   <refund-index v-show="indexMode"  hide_create="hide_create" :version="version"
    @selected="onSignupSelected" @begin-create="beginCreate">
   </refund-index> 

   <refund-details v-if="selected"  :id="selected" :can_back="can_back" 
     @btn-back-clicked="backToIndex" @refund-deleted="onRefundDeleted">
  </refund-details>

   {{-- <signup-create v-if="creating"   :course_id="course_id"
    @canceled="createCanceled" @saved="signupCreated">
      
    </signup-create> --}}

@endsection


@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               version:0,
               hide_create:true, 
               can_back:true,

               selected:0,
               creating:false,

               course_id:0,

               
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
            onSignupSelected(id){
               this.selected=id
               this.creating=false
            },
            beginCreate(course_id){
               this.selected=0
               this.course_id=course_id
               this.creating=true
            },
            onSignupSaved(signup){
                this.backToIndex()
            },
            onRefundDeleted(){
                this.backToIndex()
            },
            createCanceled(){
               this.backToIndex()
            },
            signupCreated(signup){
               this.backToIndex()
            },
            backToIndex(){
                 this.selected=0
                 this.creating=false
                 this.version +=1
            }

        },
    

    })
  </script>


@endsection