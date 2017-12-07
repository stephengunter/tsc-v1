<template>
<div>
   <user :id="id" :can_edit="userSettings.can_edit" :can_back="userSettings.can_back"  
      @saved="onUserSaved"  :role="userSettings.role"
      @user-loaded="onUserLoaded" @user-deleted="onUserDeleted"
      @btn-back-clicked="onBtnBackClicked">
   </user>

   <div  v-if="userLoaded" id="tabUser" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a @click="activeIndex=0" href="#contactinfo" data-toggle="tab">聯絡資訊</a>
                    </li>
                    <li class="">
                         <a @click="activeIndex=1" href="#signupRecord" data-toggle="tab">報名紀錄</a>
                    </li>
                </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="contactinfo">
                    <contact-info v-if="activeIndex==0"  
                     :id="contactInfoSettings.id" :user_id="contactInfoSettings.user_id" 
                     :canEdit="contactInfoSettings.canEdit" :show_residence="contactInfoSettings.show_residence" 
                     @created="onContactInfoCreated" @deleted="onContactInfoDeleted" >             
                    </contact-info>
                </div>
                <div class="tab-pane fade" id="signupRecord">
                    <user-signups v-if="activeIndex==1" :user_id="id" ></user-signups>
                </div>              
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
    
    import UserComponent from '../../components/user/user.vue'
    import contact_info from '../../components/contactInfo/contactInfo.vue'
    import UserSignups from '../../components/user/signups.vue'
    
    export default {
        name: 'UserDetails',
        components: {
            'user':UserComponent,
            'contact-info':contact_info,
            'user-signups':UserSignups
           
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            can_back: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            }
        },
        data(){
            return{
                
              
                user:{
                   contact_info:0
                },
                userSettings:{
                    can_edit:true,
                    can_back:true,
                    role:'User'
                },
               
                userLoaded:false,
                activeIndex:0,
              
                contactInfoSettings:{
                    id:0,
                    user_id:0,
                    can_edit:true,

                    show_residence:true,
                },
                signupSettings:{
                   
                }
            }
        },
        beforeMount(){
          
           this.init()
        },
        watch: {
            'id': 'init'
        },
        methods:{
            init(){
                
                this.activeIndex=0
                this.user={
                   contact_info:0
                }
                this.userLoaded=false
               
                
                this.userSettings={
                    can_edit:true,
                    can_back:this.can_back,
                    role:'User'
                }

                this.contactInfoSettings={
                    id:0,
                    user_id:this.id,
                    can_edit:true,
                    show_residence:true,
                }
               
               
            },
            
            onUserLoaded(user){
                 this.user=user
                 this.setContactInfo(user.contact_info)
                 this.userLoaded=true
            },
            onUserSaved(user){  
                this.user=user   
                this.$emit('user-saved',user)        
            },  
            onUserDeleted(){
                this.$emit('user-deleted') 
            },
            setContactInfo(contactInfoId){
                let id = Helper.tryParseInt(contactInfoId)
                if(id){
                    this.user.contact_info=null
                }else{
                     this.user.contact_info=id
                }
                
                this.contactInfoSettings.id=id
            },
            onContactInfoCreated(contactInfoId){
                this.setContactInfo(contactInfoId)
            },
            onContactInfoDeleted(){ 
                this.setContactInfo(null)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            
        }
        
    }
</script>
