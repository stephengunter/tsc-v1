<template>
   <div v-if="bill" class="panel panel-default">
      <div class="panel-heading">
         <span class="panel-title">
            <h4 v-html="title"></h4>
         </span> 
              
         <div>
            <button v-show="bill.payed" @click="btnPrintClicked" class="btn btn-warning btn-sm" >
              <span class="glyphicon glyphicon-print" aria-hidden="true"></span> 列印收據
            </button>
           
           
         </div>
      </div>  <!-- End panel-heading-->
      <show v-if="bill" v-show="readonly" :bill="bill"></show>
          
   </div>
   
    
</template>
<script>
   import Show from './show.vue'
   export default {
      name: 'BillView',
      components: {
         'show':Show,
         
      },
      props: {
         id:{
            type:Number,
            default:0
         },
         can_back:{
            type:Boolean,
            default:false
         },
      },
      data() {
         return {
            readonly:true,
            title:Helper.getIcon('bills') + '  繳費帳單',
            bill:null,

            inputSettings:{
               checker:false
            },

            form:{}
         }
      },
      beforeMount() {
        this.init()
      },
      methods: { 
         init(){
            this.readonly=true

            let getData=Bill.show(this.id)
         
            getData.then(data => {
               this.bill = new Bill(data.bill)
            })
            .catch(error=> {
              
               Helper.BusEmitError(error)
            })
          },
          btnPrintClicked(){
            alert('btnPrintClicked')
          }
			
         
      }
   }
</script>