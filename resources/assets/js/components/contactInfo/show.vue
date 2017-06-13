<template>
    <div  class="panel panel-default show-data">
       <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
              
            <div>
                 <button v-if="!id" v-show="!hide_create" @click="onBtnCreateCilcked" class="btn btn-primary btn-sm">
                      <span class="glyphicon glyphicon-plus"></span> 新增
                 </button>
                 <button v-if="contactInfo.canEdit" v-show="can_edit" @click="onBtnEditCilcked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                 </button>
                 <button v-if="contactInfo.canDelete" v-show="can_edit" @click="onBtnDeleteCilcked" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
                 </button>
               
            </div>
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <div class="row">
                 <div class="col-sm-4">
                      <label class="label-title">電話</label>
                      <p v-text="contactInfo.tel"></p>                      
                 </div>
                 <div class="col-sm-4">
                      <label class="label-title">傳真</label>
                      <p v-text="contactInfo.fax"></p>
                  </div>
                  <div class="col-sm-4">
                      <label class="label-title">最後更新</label>
                      <p v-if="!contactInfo.updated_by">{{  contactInfo.updated_at | tpeTime }}</p>
                      <p v-if="contactInfo.updated_by">
                        <a  href="#" @click.prevent="showUpdatedBy">
                          {{  contactInfo.updated_at | tpeTime }}
                        </a>                        
                      </p>
                  </div>
            </div>   <!-- End row-->
            <div class="row">
                 <div class="col-sm-6">
                      <label class="label-title">通訊地址</label>
                      <p v-text="contactInfo.addressAText"></p>                     
                 </div>
                  <div v-show="show_residence" class="col-sm-6">
                        <label class="label-title">戶籍地址</label>
                        <p v-text="contactInfo.addressBText"></p>    
                  </div>
                  
             </div>   <!-- End row-->
       </div>  <!-- End panel-body-->
   </div>
</template>

<script>
    
    export default {
        name: 'ShowContactInfo',  
        props: {
            id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            hide_create:{
               type: Boolean,
               default: false
            },  
            version: {
              type: Number,
              default: 0
            },
            show_residence:{
               type: Boolean,
               default: true
            }, 
        },
        data() {
             return {
                title:Helper.getIcon('ContactInfo') + '  聯絡資訊',  
                loaded:false,
                contactInfo:{
                   id:0
                }
            }
        },
        watch:{
          'id' : 'init'
        },    
        beforeMount(){
           this.init()
        },  
        methods: {  
            init(){
              this.loaded=false
              this.contactInfo={}
              if(this.id) this.fetchData()
              
            },
            fetchData(){
                let id=this.id
                let getData =ContactInfo.show(id)
                getData.then(data => {
                    this.contactInfo = new  ContactInfo(data.contactInfo) 
                    this.loaded = true 
                    this.$emit('loaded',this.contactInfo)                   
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
              
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.contactInfo.updated_by)
            },
            onBtnCreateCilcked(){
                this.$emit('begin-create')
            },
            onBtnEditCilcked(){
                this.$emit('begin-edit')
            },
            onBtnDeleteCilcked(){
                this.$emit('btn-delete-clicked')              
            },
        }
        

    }
</script>