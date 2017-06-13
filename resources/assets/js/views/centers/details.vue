<template>
<div>
   <center :id="id" :can_edit="centerSettings.can_edit" :can_back="centerSettings.can_back"  
      @saved="onCenterSaved"  
      @loaded="onCenterLoaded" @deleted="onCenterDeleted"
      @btn-back-clicked="onBtnBackClicked">
   </center>

   <div  v-if="centerLoaded" id="tabCenter" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a @click="activeIndex=0" href="#contactinfo" data-toggle="tab">聯絡資訊</a>
                    </li>
                </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="contactinfo">
                    <contact-info v-if="activeIndex==0"  
                     :id="contactInfoSettings.id" :center_id="contactInfoSettings.center_id" 
                     :canEdit="contactInfoSettings.canEdit" :show_residence="contactInfoSettings.show_residence" 
                     @created="onContactInfoCreated" @deleted="onContactInfoDeleted" >             
                    </contact-info>
                </div>
                            
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
    
    import CenterComponent from '../../components/center/center.vue'
    import contact_info from '../../components/contactInfo/contactInfo.vue'
    
    export default {
        name: 'CenterDetails',
        components: {
             'center':CenterComponent,
            'contact-info':contact_info,
           
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
                
              
                center:{
                   contact_info:0
                },
                centerSettings:{
                    can_edit:true,
                    can_back:true,
                    role:'Center'
                },
               
                centerLoaded:false,
                activeIndex:0,
              
                contactInfoSettings:{
                    id:0,
                    center_id:0,
                    can_edit:true,

                    show_residence:false,
                },
                signupSettings:{
                    disable_edit:true
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
                this.center={
                   contact_info:0
                }
                this.centerLoaded=false
                this.activeIndex=0

                
                this.centerSettings={
                    can_edit:true,
                    can_back:this.can_back,
                    role:'Center'
                }

                this.contactInfoSettings={
                    id:0,
                    center_id:this.id,
                    can_edit:true,
                    show_residence:false,
                }
               
               
            },
            onCenterLoaded(center){
                 this.center=center
                 this.setContactInfo(center.contact_info)
                 this.centerLoaded=true
            },
            onCenterSaved(center){  
                this.center=center   
                this.$emit('center-saved',center)        
            },  
            onCenterDeleted(){
                this.$emit('center-deleted') 
            },
            setContactInfo(contactInfoId){
                let id = Helper.tryParseInt(contactInfoId)
                if(id){
                    this.center.contact_info=null
                }else{
                     this.center.contact_info=id
                }
                
                this.contactInfoSettings.id=id
            },
            onContactInfoCreated(contactInfoId){
                this.setContactInfo(contactInfoId)
            },
            onContactInfoDeleted(){ 
                this.setContactInfo(null)
            },
            onSignupSelected(id){
                let path=Signup.showUrl(id)
                Helper.newWindow(path)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            
        }
        
    }
</script>
