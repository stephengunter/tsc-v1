<template>
<div>
  <volunteer v-show="loaded" :id="id" 
      :version="version" :can_edit="can_edit" :can_back="can_back"  
      @saved="onVolunteerUpdated" @data-loaded="onDataLoaded"
      @btn-back-clicked="onBtnBackClicked" @deleted="onVolunteerDeleted" > 

  </volunteer>

  <div v-if="loaded" id="tabVolunteer" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a @click="activeIndex=0" href="#user" data-toggle="tab">個人資料</a>
                </li>
                <li>
                     <a @click="activeIndex=1" href="#contactinfo" data-toggle="tab">聯絡資訊</a>
                </li>
                <li>
                     <a @click="activeIndex=2" href="#centers" data-toggle="tab">所屬中心</a>
                </li>
                
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="user">
                    <user v-if="activeIndex==0" :id="id" :can_edit="userSettings.can_edit" :can_back="userSettings.can_back"  
                      :hide_delete="userSettings.hide_delete"
                      @saved="onUserSaved"  :role="userSettings.role"
                      @user-loaded="onUserLoaded" >
                      
                    </user>
                </div>
                <div class="tab-pane fade" id="contactinfo">
                    <contact-info v-if="activeIndex==1"  
                     :id="contactInfoSettings.id" :user_id="contactInfoSettings.user_id" 
                     :canEdit="contactInfoSettings.canEdit" :show_residence="contactInfoSettings.show_residence" 
                     @created="onContactInfoCreated" @deleted="onContactInfoDeleted">             
                    </contact-info>
                </div>
                <div class="tab-pane fade" id="centers">
                    <user-center v-if="activeIndex==2" :user_id="id" :role='role'>
                    </user-center>
                </div>                 
            </div>
        </div>
  </div>



  


</div>
</template>

<script>
    import VolunteerComponent from '../../components/volunteer/volunteer.vue'
    import UserComponent from '../../components/user/user.vue'
    import ContactInfoComponent from '../../components/contactInfo/contactInfo.vue'
    import UserCenterView from '../../components/user-center/view.vue'
    
    
    export default {
        name: 'VolunteerDetails',
        components: {
           'volunteer' : VolunteerComponent,
           'user' : UserComponent,
           'contact-info' : ContactInfoComponent,
           'user-center':UserCenterView
         
        },
        props: {
            id: {
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
               role:Volunteer.role(),
               readonly:true,
               volunteer:null,
               version:0,

               activeIndex:0,
               user:{
                  contact_info:0,
               },

               userSettings:{
                    can_edit:true,
                    can_back:false,
                    hide_delete:true,
                    role:this.role
               },
               contactInfoSettings:{
                    id:0,
                    user_id:0,
                    can_edit:true,
                    can_back:false,
                    show_residence:true,
               },

               backTuitionSettings:{
                  hide_create:false
               }
            }
        },
        computed:{
          
        },
        beforeMount(){
           this.init()
        },
        methods: {
            init(){
              this.loaded=false
              this.readonly=true
              this.activeIndex=0

              this.volunteer=null
              this.user={
                  contact_info:0,
               }


            },
            toBoolean(val){
               return val=='true'
            },
            onDataLoaded(volunteer){
                this.loaded=true
                this.volunteer=volunteer
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
            onVolunteerUpdated(){
               this.init()
            },
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onVolunteerDeleted(){
               this.$emit('volunteer-deleted')
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.volunteer.updated_by)
            },
            onTuitionChanged(){
                this.current_version += 1               
            },
            onRefundChanged(){
                this.current_version += 1               
            },
            onUserLoaded(user){
               this.user=user
               this.setContactInfo(user.contact_info)
            },
            onUserSaved(user){
                this.user=user
                this.version += 1
            },
            setContactInfo(contactInfoId){
                let id = Helper.tryParseInt(contactInfoId)
                if(id){
                    this.user.contact_info=null
                }else{
                     this.user.contact_info=id
                }
                
                this.contactInfoSettings.id=id
                this.contactInfoSettings.user_id=this.user.id
            },
            onContactInfoCreated(contactInfoId){
                this.setContactInfo(contactInfoId)
            },
            onContactInfoDeleted(){ 
                this.setContactInfo(null)
            },
            
        }, 

    }
</script>