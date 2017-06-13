<template>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4 v-html="title"></h4>                  
             </span>           
        </div>
        <div v-if="loaded" class="panel-body">
            <form class="form-horizontal" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="form-group">
                        <label  class="col-md-2 control-label" >市話</label>
                        <div class="col-md-8">
                           <input type="text" name="contactInfo.tel" v-model="form.contactInfo.tel" class="form-control"  >
                           <small class="text-danger" v-if="form.errors.has('contactInfo.tel')" v-text="form.errors.get('contactInfo.tel')"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" >傳真</label>
                        <div class="col-md-8">
                            <input type="text" name="contactInfo.fax" v-model="form.contactInfo.fax" class="form-control"  >
                            <small class="text-danger" v-if="form.errors.has('contactInfo.fax')" v-text="form.errors.get('contactInfo.fax')"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" >通訊地址</label>
                        <div class="col-md-8  form-inline">
                            <address-info v-if="loaded" :id="parseInt(form.contactInfo.contactAddress)" name="通訊地址"
                                @saved="contactAddressSaved" @editting="onAddressEditting"
                                @canceled="onAddressEndEdit" @deleted="contactAddressDeleted">              
                            </address-info>
              
                        </div>
                    </div>   <!--end 通訊地址 -->
                    <div v-show="show_residence"  class="form-group">
                        <label class="col-md-2 control-label" >戶籍地址</label>
                        <div class="col-md-8 form-inline">
                            <address-info v-if="loaded" :id="parseInt(form.contactInfo.residenceAddress)" name="戶籍地址"
                               @saved="residenceAddressSaved" @editting="onAddressEditting" 
                               @canceled="onAddressEndEdit" @deleted="residenceAddressDeleted">
                            </address-info>                    
                        </div>
                    </div>   <!--end 戶籍地址 -->
                  <div class="form-group">
                        <div class="col-md-2">
                          <input type="hidden" name="contactInfo.user_id" v-model="form.contactInfo.user_id">
                          <input type="hidden" name="contactInfo.center_id" v-model="form.contactInfo.center_id">
                        </div>   

                        <div v-show="!hideButtons" class="col-md-8">
                            <button class="btn btn-success"  type="submit">確認送出</button>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <button class="btn btn-default"  @click.prevent="cancelEdit">取消</button>
                        </div>   
                  </div>  
                    
                
            </form>
        </div>  <!-- panel-body -->
    </div>
 
     
</template>
<script>
    import Address from '../../components/Address.vue'
    export default {
        name: 'EditContactInfo',
        components: {
            'address-info': Address
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            show_residence:{
               type: Boolean,
               default: false
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            user_id:{
              type: Number,
              default: 0
            },
            center_id:{
              type: Number,
              default: 0
            }
        },
        data() {
            return {
                title:Helper.getIcon('Users')  + '  編輯聯絡資訊',
              
                loaded:false,
                form:{},

                hideButtons:false

            }
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init(){
                this.loaded=false
                this.hideButtons=false
                this.form = new Form({
                    contactInfo:{}
                })
                
                this.fetchData()
              
            },
            fetchData() {
                let id=this.id
                let getData = null
                if(id){
                   getData=ContactInfo.edit(id)
                }
                else {
                   getData=ContactInfo.create()
                }
             
                getData.then(data => {
                    let contactInfo=data.contactInfo 
                    contactInfo.user_id=this.user_id   
                    contactInfo.center_id=this.center_id   

                    this.form.contactInfo= contactInfo               
                    this.loaded=true
                })
                .catch(error=> {
                    Helper.BusEmitError(error)                   
                    this.loaded=false
                })
                
            },
            onAddressEditting(){
              this.hideButtons=true
            },
            onAddressEndEdit(){
              this.hideButtons=false
            },
            contactAddressDeleted(){
              this.form.contactInfo.contactAddress = 0
              this.saveContactInfo(true);
              this.hideButtons=false
            },
            residenceAddressDeleted() {
              this.form.contactInfo.residenceAddress = 0
              this.saveContactInfo(true);
            },
            contactAddressSaved(address) {
               this.form.contactInfo.contactAddress = address.id
               this.saveContactInfo(true);
               this.hideButtons=false
            },
            residenceAddressSaved(address) {
              this.form.contactInfo.residenceAddress = address.id
              this.saveContactInfo(true);
              this.hideButtons=false
            },
            
            onSubmit() {
                this.saveContactInfo()
            },

            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
           
            cancelEdit(){
                this.$emit('canceled')
            },
            saveContactInfo(stay) {
                let id = this.form.contactInfo.id
                let create=true
                let save=null
                if (Helper.tryParseInt(id) > 0) {
                    create=false
                    save=ContactInfo.update(this.form , id)
                }else{
                    create=true
                    save=ContactInfo.store(this.form)
                }
                save.then(contactInfo => {
                    if(create){
                       this.$emit('created',contactInfo,stay)
                    }else{
                       this.$emit('updated',contactInfo,stay)
                    }
                    
                    this.form.contactInfo=contactInfo    
                    this.hideButtons=!stay                
                    
                    Helper.BusEmitOK('資料已存檔')
                })
                .catch(error => {
                   Helper.BusEmitError(error,'存檔失敗')
                })
                    
            }




        },

    }
</script>