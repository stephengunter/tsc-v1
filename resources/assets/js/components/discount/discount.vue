<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><span class="glyphicon glyphicon-euro" aria-hidden="true"></span> 折扣管理</h4>
            </span>
            
            <div>
                <button class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 99%;">
             <thead> 
                <tr> 
                   
                    <th style="width:15%">名稱</th> 
                    <th style="width:20%">身分</th> 
                    <th style="width:15%">折扣</th>
                    <th style="width:15%">備註</th>
                    <th style="width:10%">狀態</th>
                    <th style="width:15%">最後更新</th> 
                    <th style="width:10%"></th>
                </tr> 
            </thead>
            <tbody> 
                <edit-discount v-for="discount in discountList"  :discount="discount" 
                     @saved="discountUpdated"  @btnDeleteClicked="btnDeleteClicked" >
                </edit-discount>
                <edit-discount v-if="creating"  @saved="discountCreated"  @endEdit="endCreate" > </edit-discount> 
            
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteDiscount"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
          <div slot="modal-header" class="modal-header modal-header-danger">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="closeConfirm">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
             <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <h3 v-text="confirmMsg"> </h3>
        </div>
    </modal>

</div>

</template>

<script>
    import EditClassroom from '../../components/discount/edit-discount.vue'
    export default {
        name: 'discount',
        components: {
             'edit-discount':EditClassroom,
             'modal': Modal,
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.discountList.length) return true
                return false    
            }
        },
        data() {
            return {
                loaded:false,
                creating:false,
                discountList:[],
                showConfirm:false,
                confirmMsg:'',
                deleteId:0,
                

                center:0
             
            }
        },
        methods: {
            init() {
                this.loaded=false

                this.creating=false
                this.showConfirm=false
                this.discountList=[]
                this.confirmMsg=''
                this.deleteId=''

                this.fetchData()
                
            }, 
            
            fetchData() {
                let url = '/api/discounts'  
                axios.get(url)
                    .then(response => {
                       this.discountList=response.data.discountList
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
           
            beginCreate(){
                this.creating=true
            },
            endCreate(){
                 this.creating=false
            },
            cancelEdit(){
               
               this.$emit('endEditDiscount')
            },
            cancelCreate(){
               this.creating=false
               
            },
            btnDeleteClicked(values){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要刪除 ' + values.name + ' 嗎？'
                    this.deleteId=values.id
                    this.showConfirm=true
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
                
            },
            closeConfirm(){
                this.showConfirm=false
            },
            deleteDiscount(){
                let url = '/api/discounts/' + this.deleteId 
                let form=new Form()
                form.delete(url)
                .then(result => {
                    this.init()
                   
                    Helper.BusEmitOK('刪除成功') 

                    this.deleteId=0;
                    this.closeConfirm();
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')

                    this.closeConfirm();
                       
                })
            },
            discountCreated(discount){    
                   this.init()
            },
            discountUpdated(discount){ 
                  this.init()
            },
            
           
        },

    }
</script>