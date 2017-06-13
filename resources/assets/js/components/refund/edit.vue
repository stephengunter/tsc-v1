<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4 v-html="title">
                       
                   </h4>  
             </span>           
        </div>
         <div class="panel-body">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>姓名</label>
                            <input type="text" :value="studentName"  class="form-control" disabled>
                            
                        </div>
                    </div>
                    
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>原報名課程</label>
                            <input type="text" :value="courseName"  class="form-control" disabled>
                            
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>原繳學費</label>
                            <input type="text" :value="formatMoney(signup.tuition)"  class="form-control" disabled>
                            
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>原繳材料費</label>
                            <input type="text" :value="formatMoney(signup.cost)"  class="form-control" disabled>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>申請日期</label>
                            <div>
                                <date-picker :option="datePickerOption" :date="date" ></date-picker>
                            </div>
                             <small class="text-danger" v-if="form.errors.has('refund.date')" v-text="form.errors.get('refund.date')"></small>
                         </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>已上課時數</label>
                            <input type="text" name="refund.courses_done" class="form-control" v-model="refund.courses_done" v-on:change="countRefund">
                            <small class="text-danger" v-if="form.errors.has('refund.courses_done')" v-text="form.errors.get('refund.courses_done')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>總時數</label>
                            <input type="text" name="refund.courses_total" class="form-control"  v-model="refund.courses_total" disabled>
                            <small class="text-danger" v-if="form.errors.has('refund.courses_total')" v-text="form.errors.get('refund.courses_total')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>退費比例</label>
                            <input type="text" name="refund.points" class="form-control" v-model="refund.points" disabled>
                            <small class="text-danger" v-if="form.errors.has('refund.points')" v-text="form.errors.get('refund.points')"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>收付方式</label>
                            <select  v-model="refund.pay_by" @change="onPaywayChange"  name="refund.pay_by" class="form-control" >
                                <option v-for="item in payOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>手續費</label>
                            <input type="text" name="refund.charge" class="form-control" v-model="refund.charge" >
                            <small class="text-danger" v-if="form.errors.has('refund.charge')" v-text="form.errors.get('refund.charge')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>可退學費</label>
                            <input type="text" name="refund.tuition" class="form-control" v-model="refund.tuition">
                            <small class="text-danger" v-if="form.errors.has('refund.tuition')" v-text="form.errors.get('refund.tuition')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>可退材料費</label>
                            <input type="text" name="refund.cost" class="form-control" v-model="refund.cost">
                            <small class="text-danger" v-if="form.errors.has('refund.cost')" v-text="form.errors.get('refund.cost')"></small>
                        </div>
                    </div>
                    
                </div>
                 <div v-show="payByAccount" class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>匯款銀行</label>
                            <input type="text" name="bank_branch" class="form-control" v-model="refund.bank_branch"  >
                            <small class="text-danger" v-if="form.errors.has('bank_branch')" v-text="form.errors.get('bank_branch')"></small>
                   
                        </div>
                    </div>
            
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>匯款戶名</label>
                            <input type="text" name="account_owner" class="form-control" v-model="refund.account_owner"  >
                            <small class="text-danger" v-if="form.errors.has('account_owner')" v-text="form.errors.get('account_owner')"></small>
                   
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>匯款帳號</label>
                            <input type="text" name="account_number" class="form-control" v-model="refund.account_number"  >
                            <small class="text-danger" v-if="form.errors.has('account_number')" v-text="form.errors.get('account_number')"></small>
                        </div>
                    </div>
            
                </div>
                <div v-if="!creating" class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>狀態</label>
                            <select   class="form-control" v-model="refund.status">
                                <option v-for="item in statusOptions" :value="item.value" v-text="item.text" ></option>
                            </select>
                        </div>
                    </div>
            
                    <div class="col-sm-3">
                        
                    </div>
                    <div class="col-sm-3">
                        
                    </div>
            
                </div>
                <div class="row">
                    <div class="col-sm-4">
                         <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         <button class="btn btn-default" @click.prevent="endEdit">取消</button>
                    </div>
                    <div class="col-sm-4">
                        
                    </div>

                </div>
                    
                
            </form>
        </div>
    </div>
    
</template>
<script>
    export default {
        name: 'CreateRefund',
        props: {
            id:{
               type: Number,
               default: 0
            },
            creating:{
               type: Boolean,
               default: false
            },
        },
        
        data() {
            return {
                title:'',
                loaded:false,

                signup:{},
                refund:{},
               
                form: new Form({
                   refund:{}
                }),
                
                courseName:'',
                studentName:'',

                payOptions:[],
                statusOptions:[],
                datePickerOption:{},

                date: {
                    time: ''
                },

            }
        },
        computed: {
            payByAccount() {
                 if(parseInt(this.refund.pay_by)==1) return true
                  return false
            }
        },
        
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                if(this.creating){
                    this.title=Helper.getIcon('Refunds') + '  新增退費申請'
                }else{
                    this.title=Helper.getIcon('Refunds') + '  編輯退費申請'
                }
                this.loaded=false
               
                this.form = new Form({
                    refund:{}
                })

                this.fetchData() 
                this.datePickerOption=Helper.datetimePickerOption()
                
            },
            countRefund(){
                let done= Number(this.refund.courses_done)
                if(!done){
                   
                    this.setRefund(100,this.signup.tuition,this.signup.cost)
                      return   
                }   

                let total=Number(this.refund.courses_total)

                if(total){
                    let ratio=Refund.countRefundRatio(done,total)
                    let points=100 * ratio
                  

                    let tuition=Math.ceil(ratio * this.signup.tuition)
                   

                    this.setRefund(points,tuition,this.signup.cost)
                }  
            },
            setRefund(points,tuition,cost){
                this.refund.points=  this.formatMoney(Number(points).toFixed(2))
                this.refund.tuition=this.formatMoney(tuition)
                this.refund.cost=this.formatMoney(cost)

            },
            fetchData() {
                let getData=null
                if(this.creating){
                    getData=Refund.create(this.id)
                }else{
                    getData=Refund.edit(this.id)
                }
                
                getData.then(data => {
                    this.refund=data.refund

                    this.setSignup(data.signup)
                    
                   
                    this.payOptions=data.payOptions
                    this.statusOptions=data.statusOptions

                    this.date.time=this.refund.date

                    this.countRefund()

                    this.refund.charge=this.formatMoney(this.refund.charge)

                    this.loaded=true
                   
                })
                .catch(error=> {
                    Helper.BusEmitError(error)                        
                })
            },
            setSignup(signup){
                this.signup = signup
                this.courseName=Course.getFormatedCourseName(signup.course,true)
                this.studentName=signup.user.profile.fullname
            },
            formatMoney(money)
            {
                return Helper.formatMoney(money)  
            },
            clearErrorMsg(name) {
               this.form.errors.clear(name)
            },
            onPaywayChange(){
                if(!this.payByAccount){
                   this.clearErrorMsg()
                }
            },
            checkInput(){
                if(this.payByAccount){
                    let errors={}
                    if(!this.refund.bank_branch){
                       errors.bank_branch=['必須填寫匯款銀行']
                    }
                    if(!this.refund.account_owner){
                       errors.account_owner=['必須填寫匯款戶名']
                    }
                    if(!this.refund.account_number){
                       errors.account_number=['必須填寫匯款帳號']
                    }
                    this.form.onFail(errors)
                }
                
            },
            onSubmit() {
                this.checkInput()
               
                if(this.form.errors.any()) {
                   return false
                }


                this.submitForm()
            },
            submitForm() {
                this.refund.date=this.date.time
                if(!this.refund.charge){
                    this.refund.charge=0
                }
                this.form = new Form({
                    refund:this.refund
                })

                let save=null
                if(this.creating){
                    save=Refund.store(this.form)
                }else{
                    let id=this.id
                    save=Refund.update(this.form, id)
                }
                
                save.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
           
            endEdit(){
                 this.$emit('canceled')
            }




        },

    }
</script>