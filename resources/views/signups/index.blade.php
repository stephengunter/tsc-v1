@extends('layouts.master')

@section('content')
  
   <signup-index v-show="indexMode"   :version="version"
    @selected="onSignupSelected" @begin-create="beginCreate">
   </signup-index> 

   <signup-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
     @btn-back-clicked="backToIndex" @signup-deleted="onSignupDeleted"
      @print-invoice="onPrintInvoice">
  </signup-details>

   <signup-create v-if="creating"   :course_id="course_id"
    @canceled="createCanceled" @saved="signupCreated">
      
    </signup-create>

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
            onSignupDeleted(){
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
            },
            onPrintInvoice(id){
               let url='/signups/' + id + '/print'
               Helper.newWindow(url)
            }

        },
    

    })
  </script>


@endsection