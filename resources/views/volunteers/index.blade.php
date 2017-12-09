@extends('layouts.master')


@section('content')

       <volunteer-index v-show="!selected"  :version="version"
           @selected="onSelected"  @begin-create="onBeginCreate">
       </volunteer-index> 
       <!-- <volunteer-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
        @btn-back-clicked="backToIndex" @volunteer-deleted="onVolunteerDeleted">
       </volunteer-details> -->
       
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
                  hide_create:false, 
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
               Helper.redirect('/users/' + id) 
               //this.selected=id
            },
            // onVolunteerDeleted(){
            //     this.backToIndex()
            // },
            onBeginCreate(){
                 Helper.redirect('/volunteers/create') 
            },
            // backToIndex(){
            //     this.version+=1
            //     this.selected=0
                 
            // }
            

        },
    

    })
  </script>


@endsection

