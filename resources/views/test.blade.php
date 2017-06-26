@extends('layouts.master')


@section('content')

       <test></test>
       
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

