<template>
<div>
    <notice v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="noticeUpdated" @loaded="onDataLoaded" :version="current_version"
     @btn-back-clicked="onBtnBackClicked" @deleted="onNoticeDeleted"  > 
     

    </notice>
    <div v-if="loaded" v-show="notice.emails" id="tabEmail" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                     <a @click="activeIndex=0" href="#courses" data-toggle="tab">Email課程名單</a>
                </li>
                 <li>
                     <a @click="activeIndex=1" href="#mail_content" data-toggle="tab">Email內容</a>
                </li>
            </ul>
           
               
         
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="courses">
                    <p>
                     <span v-for="course in notice.courseNames">
                       {{ course }}   &nbsp;
                     </span>
                   </p>
                </div>
                <div class="tab-pane fade active in" id="mail_content">
                    <mail-content v-if="activeIndex==1"   :notice_id="id"></mail-content>
                </div>               
            </div>
        </div>
    </div>
 </div>   
</template>
<script>
    import Notice from '../../components/notice/notice.vue'
    import MailContent from '../../components/email/content.vue'
   
    export default {
        name: 'NoticeDetails',
        components: {
            Notice,
            'mail-content':MailContent
           
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
               notice:null,
               current_version:0,

               activeIndex:0,
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
            onDataLoaded(notice){
                this.loaded=true
                this.notice=notice
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