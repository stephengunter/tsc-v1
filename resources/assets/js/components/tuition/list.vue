<template>
<div>
    <data-viewer v-if="loaded"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
         
         <div class="form-inline" slot="header">
              <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="btnViewMoreClicked">
                  <span v-if="viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                  <span v-if="!viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
              </button>
               
         </div>
         <template scope="props">
            <tr>
                <td v-if="can_select">
                    <button @click.prevent="selected(props.item.id)" class="btn btn-primary btn-xs">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </button>
                </td> 
                <td> {{ props.item.date }} </td>
                <td>{{ props.item.amount | formatMoney }}</td>
                <td>{{ props.item.textPayBy }}</td>
                <td v-if="viewMore">{{ props.item.bank_branch }}</td>
                <td v-if="viewMore">{{ props.item.account_owner }}</td>
                <td v-if="viewMore">{{ props.item.account_number }}</td>
                <td v-if="!viewMore">
                  <a v-if="props.item.updated_by" href="#" @click.prevent="showUpdatedBy(props.item.updated_by)" >
                      {{   props.item.updated_at|tpeTime  }}
                  </a>
                  <span v-else>{{   props.item.updated_at|tpeTime  }}</span>
                  
                </td>     
            </tr>
        </template>

    </data-viewer>

    
</div>
</template>

<script>
     
    export default {
        name: 'TuitionList',
        props: {
            signup_id: {
              type: Number,
              default: 0
            },
            refund: {
              type: Boolean,
              default: false
            },
            hide_create: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            },
            can_select:{
               type: Boolean,
               default: false
            },
        },
        beforeMount() {
           this.init()
        },
        watch: {
            signup_id: function (value) {
               this.searchParams.signup=value
            }
        },
        data() {
            return {
                title: Helper.getIcon('Tuitions')  + '  繳費紀錄',
                loaded:false,
                source: Tuition.source(this.refund),
                
                defaultSearch:'date',
                defaultOrder:'date',                
                create: Tuition.createUrl(this.refund),
                
                thead:[],
                filter: [{
                    title: '繳費日期',
                    key: 'date',
                }],

                summary:null,
               
                searchParams:{
                    signup : this.signup_id,
                },
                
                hasData:false,
                viewMore:false,

                 
             
            }
        },
        computed: {
            createText(){
                if(this.hide_create) return ''
                  if(this.refund) return '新增退款記錄'
                return '新增繳費記錄'
            },
        },
        methods: {
            init() {
                if(this.refund){
                    this.title=Helper.getIcon('Tuitions')  + '  退款紀錄'
                    this.filter=[{
                        title: '退款日期',
                        key: 'date',
                    }]
                } 
                this.loaded=false
                this.hasData=false   
                this.thead= Tuition.getThead(this.can_select,this.refund)

                this.searchParams={
                            signup : this.signup_id,
                        }


                this.loaded=true
                
            },
            onDataLoaded(data){
                this.hasData = data.model.total > 0
            }, 
            btnViewMoreClicked(){
                this.viewMore=!this.viewMore
                for (var i = this.thead.length - 1; i >= 0; i--) {
                    if(!this.thead[i].static){
                        this.thead[i].default = !this.thead[i].default
                    }
                    
                }
            },
            selected(id){
                this.$emit('selected',id)
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },
           
            closeConfirm(){
                this.showConfirm=false
            },
            showUpdatedBy(updated_by){
                Bus.$emit('onShowEditor', updated_by)
            },
            
           
        },

    }
</script>