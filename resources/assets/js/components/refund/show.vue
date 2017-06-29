<template>
  

  <div v-if="loaded" class="panel panel-default show-data">
      <div class="panel-heading">
          <span class="panel-title">
             <h4 v-html="title">
               
             </h4>
          </span>
                
          <div v-if="hasData">
              <button v-show="can_back"  @click="onBtnBackClick" class="btn btn-default btn-sm" >
                 <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                 返回
              </button>
              <button  @click="btnPrintClicked" class="btn btn-warning btn-sm" >
                 <span class="glyphicon glyphicon-print" aria-hidden="true"></span> 列印
              </button>
              <button  v-if="refund.canEdit"  v-show="can_edit" @click="btnEditClicked" class="btn btn-primary btn-sm" >
                  <span class="glyphicon glyphicon-pencil"></span> 編輯
              </button>
              <button v-if="refund.canDelete" v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                  <span class="glyphicon glyphicon-trash"></span> 刪除
              </button>
              
          </div>
          <div v-else>
              <button v-show="can_edit"  @click="btnCreateClicked" class="btn btn-primary btn-sm" >
                      <span class="glyphicon glyphicon-plus"></span> 新增
              </button>
          </div>
      </div>  <!-- End panel-heading-->
      <div class="panel-body" v-show="hasData">
         
              <div class="row">
                   <div class="col-sm-3">
                        <label class="label-title">單號</label>
                        <p>{{ refund.number }}</p>                   
                   </div>
                    <div class="col-sm-3">
                        <label class="label-title">狀態</label>
                        <p v-html="refund.statusLabelHtml"></p>
                    </div>
                    
                    <div class="col-sm-3">
                        <label class="label-title">最後更新</label>
                        <p v-if="!refund.updated_by"> {{   refund.updated_at|tpeTime  }}</p>
                        <p v-else>
                          <a  href="#" @click.prevent="showUpdatedBy" >
                              {{   refund.updated_at|tpeTime  }}
                          </a>
                          
                        </p>          
                    </div>
                    
              </div>   <!-- End row-->
              
              <div class="row">
                  <div class="col-sm-3">
                        <label class="label-title">申請日期</label>
                         <p>{{ refund.date }}</p>    
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">已上課時數</label>
                        <p>{{ refund.courses_done }}</p>                      
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">總時數</label>
                        <p>{{ refund.courses_total }}</p>                  
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">退費比例</label>
                        <p>{{ refund.points }}</p>                     
                   </div>
              </div>   <!-- End row-->
              <div class="row">
                  <div class="col-sm-3">
                        <label class="label-title">收付方式</label>
                         <p>{{ refund.textPayBy }}</p>    
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">手續費</label>
                        <p>{{ refund.charge }}</p>                
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">可退學費</label>
                        <p>{{ refund.tuition }}</p>                   
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">可退材料費</label>
                        <p>{{ refund.cost }}</p>                   
                   </div>
              </div>   <!-- End row-->
              <div class="row">
                  <div class="col-sm-3">
                        <label class="label-title">應退總金額</label>
                        <p>{{ refund.total }}</p>   
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">匯款銀行</label>
                        <p>{{ refund.bank_branch }}</p>                      
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">匯款戶名</label>
                        <p>{{ refund.account_owner }}</p>               
                   </div>
                   <div class="col-sm-3">
                        <label class="label-title">匯款帳號</label>
                        <p>{{ refund.account_number }}</p>                   
                   </div>
              </div>   <!-- End row-->
              
              
              
              
             
         
     </div><!-- End panel-body-->


     
  </div>
</template>

<script>
    export default {
        name: 'ShowRefund',
        props: {
            id: {
              type: Number,
              default: 0
            },
            version: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            can_back:{
               type: Boolean,
               default: true
            },
        },
        data() {
            return {
               loaded:false,
               title:Helper.getTitleHtml('Refunds'),
               refund:null,
            }
        },
        computed: {
            hasData() {
            
              if(!this.refund) return false
              if( Helper.tryParseInt(this.refund.signup_id) < 1 ) return false
               return true
            },
        }, 
        watch:{
            'version' : 'init'
        },
        beforeMount(){
           this.init()
        },
        methods: {
           init(){
              this.loaded=false
              this.refund=null
              
              this.fetchData()
           },
           fetchData() {
                let getData = Refund.show(this.id)  
                getData.then(data => {
                   let refund= data.refund
                   this.$emit('dataLoaded',refund)

                   this.refund=new Refund(refund)                   
                   this.loaded = true  
                                         
                })
                .catch(error=> {
                      Helper.BusEmitError(error)
                })
            },
            btnCreateClicked(){
                this.$emit('begin-create')
            },
            btnEditClicked(){    
                this.$emit('begin-edit')
            },
            btnDeleteClicked(){
                 let values={
                    name: this.refund.number,
                    id:this.id
                }
               this.$emit('begin-delete',values)
            
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.refund.updated_by)
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },
            btnPrintClicked(){
               this.$emit('print-refund',this.id)
            }
        }, 

    }
</script>