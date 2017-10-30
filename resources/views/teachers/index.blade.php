@extends('layouts.master')


@section('content')
   
    <teacher-index v-show="!selected" :hide_create="indexSettings.hide_create" 
          :group="group"          :version="version"
        @selected="onSelected"  @begin-create="onBeginCreate">
    </teacher-index> 
    <teacher-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
    @btn-back-clicked="backToIndex" @teacher-deleted="onTeacherDeleted">
    </teacher-details>
       
@endsection



@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
                group:false,

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
            this.init() 
        },
        methods: {
            init(){
                @if(isset($group) && $group)

                this.group=true

                @endif

                
            },
            
            onSelected(id){
               this.selected=id
            },
            onTeacherDeleted(){
                this.backToIndex()
            },
            onBeginCreate(){
                 Helper.redirect('/teachers/create') 
            },
            backToIndex(){
                this.version+=1
                this.selected=0
                 
            }
            

        },
    

    })
  </script>


@endsection

