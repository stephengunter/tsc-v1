<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>
            <div class="form-inline">
                <div class="form-group">
                    <select @change="onParamChanged"  v-model="searchParams.center" style="width:auto;" class="form-control selectWidth">
                        <option v-for="(item,index) in center_options" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div> 

            </div>
            <div>
                <button v-if="canCreate" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table  class="table table-striped" >
                <thead> 
                    <tr> 
                        <th style="width:25%">名稱</th> 
                        <th style="width:10%">不限課程</th>
                        <th style="width:12%">第一階段折扣</th>
                        <th style="width:12%">第二階段折扣</th>
                        
                        <th style="width:10%">狀態</th>
                        <th style="width:10%" v-show="!edittingOrder">
                            順序
                            <button v-if="canEditOrder" v-show="hasData" @click="beginEditOrder" class="btn btn-primary btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                            </button>
                        </th>
                        <th style="width:10%" v-show="edittingOrder">
                            順序
                            <button @click="onSubmitDisplayOrders" class="btn btn-success btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-floppy-disk" ></span>
                            </button>
                            <button @click="cancelEditOrder" class="btn btn-default btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                            </button>
                        </th>
                        <th style="width:15%">最後更新</th> 
                        <th style="width:10%"></th>
                    </tr> 
                </thead>
                <tbody> 
                    <edit v-if="creating" :center_id="searchParams.center"
                        @saved="onCreated"  @canceled="onCreateCanceled" > 
                    </edit> 
                    <edit v-for="(discount,index) in discountList" :key="index" 
                        :index="index" :discount="discount" 
                        :can_edit="canEdit" :editting_order="edittingOrder"
                        @begin-edit="onBeginEdit"
                        @canceled="onEditCanceled"  @clear-error="clearErrorMsg"
                        @saved="onUpdated"  >
                    </edit>
                    
                </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
   
    

</div>

</template>

<script>
    import Edit from '../../components/discount/edit.vue'
    export default {
        name: 'DiscountIndex',
        components: {
             Edit,
        },
        props: {
            center_options:{
                type: Array,
                default: []
            }, 
        },
        beforeMount() {
            if(this.center_options && this.center_options.length) {
                 this.searchParams.center=this.center_options[0].value
            }
            
            this.init()
        },
        computed:{
            hasData(){
                if(this.discountList.length) return true
                return false    
            },
            canCreate(){
                if(this.editting) return false
                if(this.creating) return false
                if(this.edittingOrder) return false
                
                return this.canEdit
            },
            canEditOrder(){
                if(this.creating) return false
                if(!this.hasData) return false
                if(this.editting) return false
                return this.canEdit
            }
        },
        data() {
            return {
                title:Helper.getIcon(Discount.title()) + '  折扣管理',
                loaded:false,
                creating:false,

                searchParams:{
                    center:0
                },

                editting: false,

                canEdit:false,
                edittingOrder: false,

                discountList:[],
              
                orderOptions:{},

               
             
            }
        },
        methods: {
            init() {
                this.loaded=false
                this.creating=false
                this.editting=false
                this.edittingOrder=false
              
                this.discountList=[]
                
               
                
                
                this.fetchData()         
            }, 
            fetchData() {
                let center_id=this.searchParams.center
                let index=Discount.index(center_id)
                index.then(data => {
                    this.canEdit=data.canEdit
                    

                    data.discountList.forEach((item) => {
                       item.error=''
                    })

                    this.discountList=data.discountList
                    this.loaded = true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            onParamChanged(){
                this.fetchData()
            },
            onBeginEdit(){
                this.edittingOrder=false
                this.editting=true
            },
            onEditCanceled(){
                this.editting=false
                
            },
            beginCreate(){
                this.creating=true
            },
            onCreateCanceled(){
                 this.init()
            },
            
            onCreated(){    
                this.init()
                this.$emit('created')
            },
            onUpdated(){
                this.init()
                this.$emit('updated')
            },            
           
            beginEditOrder(){
                this.edittingOrder=true
            },
            cancelEditOrder(){
                 this.init()
            },
            onRowSelected(id){
                this.$emit('selected',id)
            },
            clearErrorMsg(index){
                let discount=this.discountList[index]
                discount.error= ''
            },
            onSubmitDisplayOrders(){
                let errors=0
                for(let i=0; i<this.discountList.length ; i++){
                    let discount=this.discountList[i]
                    let val=discount.order
                    if(isNaN(val) || val.length < 1){
                          discount.error= '必須為數字'
                          errors+=1
                    }else{
                        discount.error= ''
                    }
                   
                }
              
                if(errors==0) {
                    this.updateDisplayOrder()
                }
            },
            updateDisplayOrder(){
               
                let discounts = this.discountList.map(item=>{
                    return {
                                id:item.id,
                                order:item.order
                          }
                })

                
                let save=Discount.updateDisplayOrder(discounts)
                save.then(result => {
                        Helper.BusEmitOK()
                        this.init()
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            },
            
           
        },

    }
</script>