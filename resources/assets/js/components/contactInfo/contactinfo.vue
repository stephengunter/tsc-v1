<template>
<div>
   
    <show v-if="readOnly"  :id="contactInfo.id" 
        can_edit="can_edit"  :show_residence="show_residence"
         @begin-edit="beginEdit" @begin-create="beginCreate"
        @data-loaded="onDataLoaded"
        @btn-delete-clicked="beginDelete"  >               
       
    </show>

    <edit v-else :id="contactInfo.id" :show_residence="show_residence"
       :user_id="user_id"  :center_id="center_id"
       @created="onCreated"
       @updated="onUpdated"   @canceled="onEditCanceled" >                 
    </edit>
    
    
    
    
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteContactInfo">        
    </delete-confirm>
</div>
</template>
<script>
    import Show from '../../components/contactInfo/show.vue'
    import Edit from '../../components/contactInfo/edit.vue'


    export default {
        name:'ContactInfo',
        components: {
            Show,
            Edit,
          
        },
        props: {
            id: {
               type: Number,
               default: 0
            },
            show_residence:{
               type: Boolean,
               default: false
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            user_id:{
              type: Number,
              default: 0
            },
            center_id:{
              type: Number,
              default: 0
            }
        },
        
        
        data() {
            return {
                
                readOnly:true,
             

                contactInfo:{
                    id:0,
                },

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
            }
        },
        beforeMount(){
            
            this.init()
        },
        watch: {
            'id': 'init',
            
        },
        methods: {
            init() {
               this.readOnly=true
               this.contactInfo.id=this.id
               this.deleteConfirm.id=this.id
            },
            onDataLoaded(contactInfo){
                this.contactInfo=contactInfo
                this.$emit('data-loaded',contactInfo)
            }, 
            beginEdit() {
                this.readOnly=false
            },
            beginCreate(){
                this.readOnly=false
            },
            onEditCanceled(){
                this.readOnly=true
            },
            onCreated(contactInfo,stay){
                this.contactInfo=contactInfo
                if(!stay){
                    this.readOnly=true
                }
                this.$emit('created') 
            },
            onUpdated(contactInfo, stay){
                this.contactInfo=contactInfo
                if(!stay){
                    this.readOnly=true
                } 
                this.$emit('updated') 
            },
            beginDelete(){
                let id=this.contactInfo.id
                this.deleteConfirm.msg='確定要刪除聯絡資訊嗎？'
                this.deleteConfirm.id=id
                this.deleteConfirm.show=true                
            },
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteContactInfo(){
                let id = this.deleteConfirm.id 
                let remove= ContactInfo.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.contactInfo={
                        id:0
                    }
                    this.deleteConfirm.show=false     
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },
            

            
        }
    }
</script>
