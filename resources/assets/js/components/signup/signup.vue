<template>
<div>
    
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  :can_back="can_back"  
       :version="version"  @begin-edit="beginEdit" @dataLoaded="onDataLoaded"
       @btn-back-clicked="onBtnBackClicked"   @btn-delete-clicked="beginDelete" 
       @print-invoice="onPrintInvoice">                 
    </show>

    <edit v-else :id="id" 
       @saved="onSignupSaved"   @canceled="onEditCanceled" >                 
    </edit> 

    <delete-confirm :showing="showConfirm" :message="confirmMsg"
      @close="closeConfirm" @confirmed="deleteSignup">        
    </delete-confirm>
    

</div>
</template>
<script>
    import Show from '../../components/signup/show.vue'
    import Edit from '../../components/signup/edit.vue'


    export default {
        name:'Signup',
        components: {
            Show,
            Edit,
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
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                readOnly:true,
                confirmMsg:'',
                deleteId:0,
                showConfirm:false,    
            }
        },
        beforeMount(){
            this.init()
        },
        watch: {
            'id': 'init',
            'version':'init'
        },
        methods: {
            init() {
               this.readOnly=true
               this.confirmMsg=''
               this.deleteId=0
               this.showConfirm=false
            },      
            onDataLoaded(signup){
                this.$emit('data-loaded',signup)
            },        
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onSignupSaved(signup){
                this.init()
                this.$emit('saved',signup)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            beginDelete(values){
                this.confirmMsg='確定要刪除 ' + values.name + ' 的報名紀錄嗎？'
                this.deleteId=values.id
                this.showConfirm=true                
            },
            closeConfirm(){
                this.showConfirm=false
            },
            deleteSignup(){
                let id = this.deleteId 
                let remove= Signup.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('signup-deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },
            onPrintInvoice(){
                this.$emit('print-invoice',this.id)
            }
            
        }
    }
</script>
