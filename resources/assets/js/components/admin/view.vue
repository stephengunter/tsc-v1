<template>
<div>
    
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  :can_back="can_back"  
       :version="version"  @begin-edit="beginEdit" @dataLoaded="onDataLoaded"
       @btn-back-clicked="onBtnBackClicked"   @btn-delete-clicked="beginDelete" >                 
    </show>

    <edit v-else :id="id" 
       @saved="onSaved"   @canceled="onEditCanceled" >                 
    </edit> 

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeleteCanceled" @confirmed="deleteAdmin">        
    </delete-confirm>
    

</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'



    export default {
        name:'Admin',
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
            'version':'init'
        },
        methods: {
            init() {
               this.readOnly=true
               this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
               }
            },      
            onDataLoaded(admin){
                this.$emit('data-loaded',admin)
            },        
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onSaved(admin){
                this.init()
                this.$emit('saved',admin)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onDeleteCanceled(){
                this.deleteConfirm.show=false
            },
            beginDelete(values){
                this.deleteConfirm.msg='確定要刪除 ' + values.name + ' 的系統管理員資料嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                 
            },
            deleteAdmin(){
                this.deleteConfirm.show=false
                
                let id = this.deleteConfirm.id 
                let remove= Admin.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.init()
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
