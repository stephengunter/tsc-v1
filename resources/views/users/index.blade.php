@extends('layouts.master')


@section('content')

       <user-index v-show="!selected" :hide_create="indexSettings.hide_create" :version="version"
       @selected="onUserSelected" @add-to-role="addToRole" @begin-create="onBeginCreate">
       </user-index> 

       <user-details v-if="selected" :id="selected" :can_back="can_back" 
         @btn-back-clicked="backToIndex" @user-deleted="onUserDeleted"></user-details>

           

       
@endsection



@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               version:0,
               indexSettings:{
                  hide_create:false, 
               },
               
               indexMode:true,
               selected:0,

               can_back:true,
            }
        },
        beforeMount() {
             
        },
        methods: {
            init(){
             
            },
            
            onUserSelected(id){
               this.selected=id
            },
            addToRole(user_id,role){
                 let url=Helper.getUserToRoleUrl(user_id,role)
                 alert(url)
               
            },
            onUserDeleted(){
                this.backToIndex()
            },
            onBeginCreate(){
                 Helper.redirect('/users/create') 
            },
            backToIndex(){
                this.version+=1
                this.selected=0
                 
            }
            

        },
    

    })
  </script>


@endsection

