<template>
    <notice v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="noticeUpdated" @loaded="onDataLoaded" :version="current_version"
     @btn-back-clicked="onBtnBackClicked" @deleted="onNoticeDeleted"  > 
     

    </notice>
</template>
<script>
    import Notice from '../../components/notice/notice.vue'

    export default {
        name: 'NoticeDetails',
        components: {
            Notice,
           
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
               notice:null,
               current_version:0,

               activeIndex:0,

               refundSettings:{
                  can_back:false
               },
               backTuitionSettings:{
                  hide_create:false
               }
            }
        },
        computed:{
           hasRefundRecord(){
              if(!this.notice) return false
              if(!this.notice.hasRefund) return false
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
            onDataLoaded(notice){
                this.loaded=true
                this.notice=notice
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
            noticeUpdated(){
               this.init()
            },
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onNoticeDeleted(){
               this.$emit('notice-deleted')
            },
           
        }, 

    }
</script>