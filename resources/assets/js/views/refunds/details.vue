<template>
<div>
  <refund v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="onRefundUpdated" @data-loaded="onDataLoaded" :version="current_version"
     @btn-back-clicked="onBtnBackClicked" @refund-deleted="onRefundDeleted" > 

  </refund>

  <div id="tabSignup" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a @click="activeIndex=0" href="#signup" data-toggle="tab">報名表</a>
                </li>
                <li class="">
                     <a @click="activeIndex=1" href="#tuitions" data-toggle="tab">繳費紀錄</a>
                </li>
                <li class="">
                     <a @click="activeIndex=2" href="#tuitionsback" data-toggle="tab">退款紀錄</a>
                </li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="signup">
                    <show-signup v-if="activeIndex==0"  :id="id"
                    :version="version" :can_edit="signupSettings.can_edit"
                     :can_back="signupSettings.can_back">                      
                    </show-signup>                   
                </div>
                <div class="tab-pane fade active in" id="tuitions">
                    <tuition-view v-if="activeIndex==1" :signup_id="id"
                     :hide_create="tuitionSettings.hide_create" :can_select="tuitionSettings.can_select">
                    </tuition-view>
                </div>
                
                <div class="tab-pane fade" id="tuitionsback">
                   <tuition-view v-if="activeIndex==2" :signup_id="id"  :refund="toBoolean('true')" 
                     :hide_create="!hasRefundRecord"
                     @tuition-created="onTuitionChanged" @tuition-updated="onTuitionChanged"
                     @tuition-deleted="onTuitionChanged">
                   </tuition-view>
                </div>                
            </div>
        </div>
  </div>



  


</div>
</template>

<script>
    import refund from '../../components/refund/refund.vue'
    import ShowSignup from '../../components/signup/show.vue'
    
    export default {
        name: 'RefundDetails',
        components: {
            refund,
            'show-signup':ShowSignup

         
        },
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
               readonly:true,
               refund:null,
               current_version:0,

               activeIndex:0,

               signupSettings:{
                  can_edit:false,
                  can_back:false
               },
               tuitionSettings:{
                  hide_create:true,
                  can_select:false
               }


            }
        },
        computed:{
           hasRefundRecord(){
              if(this.refund) return true
              return false
           }
        },
        beforeMount(){
           this.init()
        },
        methods: {
            init(){
              this.loaded=false
              this.readonly=true
              this.activeIndex=0
            },
            toBoolean(val){
               return val=='true'
            },
            onDataLoaded(refund){
                this.refund=refund
                this.loaded=true
            },
            btnEditClicked(){    
              this.beginEdit()
            },
            beginEdit(){
               this.readonly=false
            },
            editCanceled(){
               this.readonly=true
            },
            onRefundUpdated(){
               this.init()
            },
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onRefundDeleted(){
               this.$emit('refund-deleted')
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.signup.updated_by)
            },
            onTuitionChanged(){
                this.current_version += 1               
            }
        }, 

    }
</script>