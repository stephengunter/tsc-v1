<template>
<div>
  <show-refund v-if="readonly"  :id="id" canEdit="true" can_back="true" :version="version"
    @btnDeleteClicked="onBtnDeleteClicked"  @dataLoaded="onDataLoaded"   @btnEditClicked="beginEdit"   @endShow="endShow" >  
  </show-refund>

  <edit-refund v-if="!readonly"  :signup_id="id" 
       @saved="refundUpdated"   @canceled="editCanceled" >                 
  </edit-refund>

  <div v-if="loaded" class="panel with-nav-tabs panel-default">
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
                    <show-signup v-if="activeIndex==0"  :id="id"></show-signup>
                   
                </div>
                <div class="tab-pane fade" id="tuitions">
                    <tuition v-if="activeIndex==1" :signup_id="id" :canEdit="false">
                    </tuition>
                    
                </div>
                <div class="tab-pane fade" id="tuitionsback">
                    <tuition v-if="activeIndex==2" refund="true" canEdit="true" :signup_id="id" 
                      @deleted="onBackTuitionChanged" @tuitionUpdated="onBackTuitionChanged">
                    </tuition>
                </div>                
            </div>
        </div>
  </div>



  


</div>
</template>

<script>
    import ShowRefund from '../../components/refund/show-refund.vue'
    import EditRefund from '../../components/refund/edit-refund.vue'
    
    import ShowSignup from '../../components/signup/show-signup.vue'
    import Tuition from '../../components/tuition/tuition.vue'
  
    export default {
        name: 'RefundDetails',
        components: {
           'show-refund':ShowRefund,
           'edit-refund':EditRefund,
           'show-signup':ShowSignup,
            Tuition,
       
        },
        props: ['id',  'canEdit'],
        data() {
            return {
               loaded:false,
               readonly:true,
               refund:null,
               version:0,

               activeIndex:0
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
            onDataLoaded(){
                this.loaded=true
            },
            btnEditClicked(){    
              this.beginEdit()
            },
            beginEdit(){
              alert('beginEdit')
               this.readonly=false
            },
            editCanceled(){
               this.readonly=true
            },
            refundUpdated(){
              this.version += 1
              this.init()
            },
            endShow(){
                this.$emit('endShow')
            },
            onBtnDeleteClicked(values){
               this.$emit('btnDeleteClicked',values)
            
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.refund.updated_by)
            },
            onBackTuitionChanged(){
               this.version += 1            
            }
        }, 

    }
</script>