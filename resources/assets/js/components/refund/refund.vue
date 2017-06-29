<template>
<div>
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  :can_back="can_back"  
       :version="version"  @begin-edit="beginEdit" @dataLoaded="onDataLoaded"
       @begin-create="beginCreate" @print-refund="onPrintRefund"
       @btn-back-clicked="onBtnBackClicked"   @begin-delete="beginDelete" >                 
    </show>

    <edit v-else :id="id" :creating="creating" 
      @saved="onSaved" @canceled="onEditCanceled"></edit>

    <delete-confirm :showing="showConfirm" :message="confirmMsg"
      @close="closeConfirm" @confirmed="deleteRefund">        
    </delete-confirm>
    
</div>    
</template>

<script>
    import Show from '../../components/refund/show.vue'
    import Edit from '../../components/refund/edit.vue'

    export default {
        name: 'Refund',
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
                creating:false,
                confirmMsg:'',
                showConfirm:false,                 
            }
        },
        methods: {
            init(){
                this.creating=false
                this.readOnly=true
                this.showConfirm=false
                this.confirmMsg=''
            },
            onDataLoaded(refund){
                this.$emit('data-loaded',refund)
            }, 
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            beginCreate(){
                this.readOnly=false
                this.creating=true
            },
            beginEdit() {

                 this.readOnly=false
            },
            onEditCanceled(){
                 this.readOnly=true
            },
            
            onSaved(refund){
                this.init()
                if(this.creating) this.$emit('refund-created',refund)
                else this.$emit('refund-updated',refund)
                
            },
            closeConfirm(){
                this.showConfirm=false
            },
            deleteRefund(){
                let id = this.id 
                let remove= Refund.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('refund-deleted')
                    this.closeConfirm()
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },
            beginDelete(values){
                 this.confirmMsg='確定要刪除退費申請 ' + values.name + ' 嗎？'
                 this.showConfirm=true               
            },
            onPrintRefund(){
                 this.$emit('print-refund',this.id)
            }
            
            
        }
    }
</script>