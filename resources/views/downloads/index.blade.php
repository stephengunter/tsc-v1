@extends('layouts.master')


@section('content')
   
    <download-index v-show="!selected" :hide_create="indexSettings.hide_create" 
           
                :version="version"
        @details="onSelected"  @begin-create="onBeginCreate">
    </download-index> 
    
       
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
                 Helper.redirect('/downloads/create') 
            },
            backToIndex(){
                this.version+=1
                this.selected=0
                 
            }
            

        },
    

    })
  </script>


@endsection

