<template>
<div>
  <signup-view v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="signupUpdated" @data-loaded="onDataLoaded" :version="current_version"
     @btn-back-clicked="onBtnBackClicked" @signup-deleted="onSignupDeleted"   > 
     

  </signup-view>

  <div v-if="loaded" id="tabSignup" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a @click="activeIndex=0" href="#tuitions" data-toggle="tab">繳費帳單</a>
                </li>
                <li class="">
                     <a @click="activeIndex=1" href="#refund" data-toggle="tab">退費申請</a>
                </li>
                <li class="">
                     <a @click="activeIndex=2" href="#tuitionsback" data-toggle="tab">退款紀錄</a>
                </li>
                <li v-if="showSubCourses" class="">
                     <a @click="activeIndex=3" href="#subCourses" data-toggle="tab">報名課程</a>
                </li>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="tuitions">
                    <bill-view :id="parseInt(signup.bill_id)"></bill-view>
                    <!-- <tuition-view v-if="activeIndex==0" :signup_id="id"  
                     @tuition-created="onTuitionChanged" @tuition-updated="onTuitionChanged"
                     @tuition-deleted="onTuitionChanged">
                    </tuition-view> -->
                </div>
                <div class="tab-pane fade" id="refund">
                    <refund-view v-if="activeIndex==1"  :id="id" :can_back="refundSettings.can_back"
                      @refund-created="onRefundChanged" @refund-updated="onRefundChanged"
                      @refund-deleted="onRefundChanged"  >    
                  
                    
                    </refund-view>
                </div>
                <div class="tab-pane fade" id="tuitionsback">
                   <tuition-view v-if="activeIndex==2" :signup_id="id"  :refund="toBoolean('true')" 
                     :hide_create="!hasRefundRecord"
                     @tuition-created="onTuitionChanged" @tuition-updated="onTuitionChanged"
                     @tuition-deleted="onTuitionChanged">
                   </tuition-view>
                </div>   
                <div class="tab-pane fade" id="subCourses">
                    <sub-course-selector v-if="activeIndex==3"
                     :can_select="subCourseSettings.can_select" :courses="signup.subCourses"  >
                    </sub-course-selector>
                </div>               
            </div>
        </div>
  </div>



  


</div>
</template>

<script>
    import SignupView from '../../components/signup/signup.vue'
    import BillView from '../../components/bill/view.vue'
    
    
    export default {
        name: 'SignupDetails',
        components: {
            'signup-view':SignupView,
            'bill-view':BillView
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
               default: false
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
               signup:null,
               current_version:0,

               activeIndex:0,

               refundSettings:{
                  can_back:false
               },
               backTuitionSettings:{
                  hide_create:false
               },

               showSubCourses:false,
               subCourseSettings:{
                   can_select:false,
                },
              
            }
        },
        computed:{
           hasRefundRecord(){
              if(!this.signup) return false
              if(!this.signup.hasRefund) return false
                  return true
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
            onDataLoaded(signup){
               
                this.loaded=true
                this.signup=signup
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
            signupUpdated(){
               this.init()
            },
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onSignupDeleted(){
               this.$emit('signup-deleted')
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.signup.updated_by)
            },
            onTuitionChanged(){
                this.current_version += 1               
            },
            onRefundChanged(){
                this.current_version += 1               
            },
            
            loadSubCourses(){

            }
        }, 

    }
</script>