@extends('layouts.master')


@section('content')

       <admin-index :options="options" 
           v-show="!selected" :hide_create="indexSettings.hide_create" :version="version"
           @selected="onSelected"  @begin-create="onBeginCreate">
       </admin-index> 
       <admin-details v-if="selected"  :id="selected" :can_back="detailsSettings.can_back" 
           @btn-back-clicked="backToIndex" @admin-deleted="onAdminDeleted">
       </admin-details>
       
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

               options:{
                   centers:[],
                   roles:[]
               }
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

            @if(isset($roleOptions))

                    this.options.roles={!! json_encode($roleOptions) !!}

            @endif
           
            @if(isset($centerOptions))

                    this.options.centers={!! json_encode($centerOptions) !!}

            @endif
        },
        methods: {
            init(){
             
            },
            
            onSelected(id){
               this.selected=id
            },
            onAdminDeleted(){
                this.backToIndex()
            },
            onBeginCreate(){
                 Helper.redirect('/admins/create') 
            },
            backToIndex(){
                this.version+=1
                this.selected=0
                 
            }
            

        },
    

    })
  </script>


@endsection

