<template>
<div>
   <user :id="id" :canEdit="canEdit"  @userLoaded="onUserLoaded" :role="role"></user>

   <div  v-if="userLoaded" id="tabUser" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a @click="activeIndex=1" href="#admin" data-toggle="tab">管理者資訊</a>
                    </li>
                    <li class="">
                        <a @click="activeIndex=2" href="#contactinfo" data-toggle="tab">聯絡資訊</a>
                    </li>
                    <li class="">
                        <a @click="activeIndex=3" href="#centers" data-toggle="tab">所屬中心</a>
                    </li>
                </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="admin">

                     <admin v-if="activeIndex==1" :id="id" ></admin>
                </div>
                <div class="tab-pane fade" id="contactinfo">

                    <contact-info v-if="activeIndex==2" @contactCreated="onContactCreated" 
                     :id="user.contact_info" @contactInfoDeleted="onContactInfoDeleted" 
                    
                     :showResidence="true" :canEdit="true">                            
                    </contact-info>
                    
                </div>

                <div class="tab-pane fade" id="centers">
                   <admin-center v-if="activeIndex==3" :id="id" ></admin-center>
                </div>
                               
            </div>
        </div>
    </div>   

</div>    
</template>
<script>
    
    import User from '../../components/user/user.vue'
    import Admin from '../../components/admin/admin.vue'
    import ContactInfo from '../../components/contactInfo/contactInfo.vue'
    import AdminCenter from '../../components/admin/admin-center.vue'

    export default {
        components: {
            User,
            Admin,
            ContactInfo,
            'admin-center':AdminCenter
        },
        name: 'UserView',
        data(){
            return{
                id:0,
                user:{},
                role:'Admin',
                canEdit:true,
                userLoaded:false,
                activeIndex:1
            }
        },
        beforeMount(){
           this.init()
        },
        watch: {
            '$route': 'init'
        },
        methods:{
            init(){
                this.id=this.$route.params.id
                this.user={}
                this.canEdit=true
                this.userLoaded=false
                this.activeIndex=1
            },
            onUserLoaded(user){
                this.user=user
                 this.userLoaded=true
            },
             
            onContactCreated(contactInfo){ 
                 this.updateUserContactInfo(contactInfo)
            },
            onContactInfoDeleted(){ 

                Bus.$emit('userDataChanged')
            },

            updateUserContactInfo(contactInfo){
                let userId=this.id
                UserScripts.updateUserContactInfo(userId,contactInfo)
                
            }
   
            
        }
        
    }
</script>
