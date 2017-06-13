<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">           
            <span class="panel-title">
                <h4>
                    <i class="fa fa-money" aria-hidden="true"></i> {{ title }}
                </h4>  
            </span>
            <div>
               
                <button v-if="form.tuition.canDelete" v-show="can_delete" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                      <span class="glyphicon glyphicon-trash"></span> 刪除
                </button>
              </div>           
        </div>        
        <div class="panel-body">
            <form v-if="loaded" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>報名課程</label>
                            <select  v-model="form.tuition.signup_id"  name="tuition.signup_id" class="form-control" >
                                <option v-for="item in signupOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label v-if="refund">退款方式</label>
                             <label v-else>繳費方式</label>
                            <select  v-model="form.tuition.pay_by"  name="tuition.pay_by" class="form-control" >
                                <option v-for="item in payOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                          <label>金額</label>
                             <input type="text" name="tuition.amount" class="form-control" v-model="form.tuition.amount"  >
                             <small class="text-danger" v-if="form.errors.has('tuition.amount')"  v-text="form.errors.get('tuition.amount')"></small>
                         </div>
                    </div>
                    <div class="col-sm-3">
                       <div class="form-group">
                            <label v-if="refund">退款日期</label>
                            <label v-else>繳費日期</label>
                            <div>
                                <date-picker  :date="payDate" :option="dateOption"></date-picker>
                            </div>
                            <small class="text-danger" v-if="form.errors.has('tuition.date')" v-text="form.errors.get('tuition.date')"></small>
                        </div>
                    </div>
                    
                </div>
                 <div v-show="payByAccount" class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>匯款銀行</label>
                            <input type="text" name="tuition.bank_branch" class="form-control" v-model="form.tuition.bank_branch"  >
                            <small class="text-danger" v-if="form.errors.has('tuition.bank_branch')" v-text="form.errors.get('tuition.bank_branch')"></small>
                   
                        </div>
                    </div>
                    
                    <div class="col-sm-3">
                        <div class="form-group">    
                            <label v-if="refund">收款人戶名</label>
                            <label v-else>匯款人戶名</label>                       
                          
                            <input type="text" name="tuition.account_owner" class="form-control" v-model="form.tuition.account_owner"  >
                            <small class="text-danger" v-if="form.errors.has('tuition.account_owner')" v-text="form.errors.get('tuition.account_owner')"></small>
                   
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label v-if="refund">收款人帳號</label>
                            <label v-else>匯款人帳號</label>    
                            <input type="text" name="tuition.account_number" class="form-control" v-model="form.tuition.account_number"  >
                            <small class="text-danger" v-if="form.errors.has('tuition.account_number')" v-text="form.errors.get('tuition.account_number')"></small>
                        </div>
                    </div>
                    
                </div>
             
               
                <div class="row">
                    <div class="col-sm-6">
                      <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                      <button class="btn btn-default" @click.prevent="onCancel">取消</button>
                    </div>
                   

                </div>
                        
            </form>
        </div>    
    </div>


    <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteTuition"  @closed="closeConfirm" ok-text="確定"
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
    export default {
        name: 'EditTuition',
        props: {
            id:{
               type: Number,
               default: 0
            },
            refund:{
               type: Boolean,
               default: false
            },
            can_delete:{
               type: Boolean,
               default: true
            },
        },
        data() {
            return {
                loaded:false,
                title:'編輯繳費紀錄',
               
                form:{
                    tuition:{}
                },

                signup:{},

                payDate: {
                    time: ''
                },
                signupOptions:[],
                payOptions:[],
               
                dateOption: {},

                confirmMsg:'',
                showConfirm:false,

            }
        },
        computed: {
            payByAccount() {
                 if(parseInt(this.form.tuition.pay_by)==1) return true
                  return false
            },
            
        },
        
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                if(this.refund) this.title='編輯退款記錄'
                this.fetchData()  
                this.dateOption= Helper.datetimePickerOption()
            },
            fetchData() {
                let getData=Tuition.edit(this.id,this.refund)
               
                getData.then(data => {

                        let signup=data.signup
                        let tuition=data.tuition
                        tuition.amount=Helper.formatMoney(tuition.amount)

                        this.payOptions=data.payOptions

                        this.signupOptions=[]
                        let formatedCourseName=signup.course.name + ' ' +  signup.course.number
                        let item={ text: formatedCourseName , value: signup.id }
                        this.signupOptions.push(item)


                        this.payDate.time = tuition.date

                        this.form = new Form({
                             tuition :tuition
                        })
                      
                        this.signup=signup

                        this.loaded=true
                       
                    })
                    .catch(error=> {
                        Helper.BusEmitError(error)                          
                    })
            },
            onCancel(){
                this.$emit('canceled')
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);

            },
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                this.form.tuition.date = this.payDate.time  

                let update=Tuition.update(this.form, this.id , this.refund)
                update.then(tuition => {
                       Helper.BusEmitOK('資料已存檔')
                       this.$emit('saved',tuition)                            
                    })
                    .catch(error => {
                      
                        Helper.BusEmitError(error,'存檔失敗') 
                    })
            },

            btnDeleteClicked(){
                let item = this.form.tuition
                let name =  Helper.formatMoney(item.amount) + '元' + ' ' + item.date  
                let action=this.refund ? '退款' : '繳費'

                this.confirmMsg='確定要刪除' + action +'紀錄 ' + name + ' 嗎？'
              
                this.showConfirm=true
            
            },
            closeConfirm(){
                this.showConfirm=false
            },
            deleteTuition(){
                let id = this.id 
                let remove= Tuition.delete(id, this.refund)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('deleted')
                    this.closeConfirm()   
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },


        },

    }
</script>