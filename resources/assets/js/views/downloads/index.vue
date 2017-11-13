<template>
    <div>
        <list v-show="indexMode" :version="version"
        @delete="beginDelete"
        @begin-create="beginCreate" @selected="onSelected">
            
        </list>

        <edit v-if="!indexMode" :id="selected" 
         @saved="onSaved" @canceled="onCancelEdit">
        </edit>

        <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
        @close="onDeleteCanceled" @confirmed="deleteDownload">        
        </delete-confirm>
    </div>
</template>



<script>

import List from '../../components/download/list.vue'
import Edit from '../../components/download/edit.vue'

export default {
    name: 'DownloadIndex',
    components: {
        List,
        Edit   
    },
    beforeMount() {
        this.init()
        
    },
    data() {
        return {
            
            selected:0,
            creating:false,
            center:0,
            centerOptions:[],
            
            downloadList:[],
            

            deleteConfirm:{
                id:0,
                show:false,
                msg:'',
            },

            version:0,
            
        }
    },
    computed:{
        indexMode(){
            if(this.creating)  return false
            if(this.selected)  return false
            return true
        }
        
    },
    methods: {
        init() {
            this.selected=0
            this.creating=false

           

            this.deleteConfirm={
                id:0,
                show:false,
                msg:''
            }
        }, 
        refresh(){
            this.init()
            this.version +=1
        },
        onSelected(id){
            this.creating=false
            this.selected=id
        },
        onSaved(){
            this.refresh()
        },
        
        beginCreate(){
            this.creating=true
        },
        onCancelEdit(){
            this.init()
        },
        beginDelete(download){
            this.deleteConfirm.msg='確定要刪除 ' + download.title + ' 嗎？'
            this.deleteConfirm.id=download.id
            this.deleteConfirm.show=true                
        },
        
        onDeleteCanceled(){
            this.deleteConfirm.id=0
            this.deleteConfirm.show=false
        },
        deleteDownload(){
            let id = this.deleteConfirm.id 
            let remove= Download.delete(id)
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
        
        
        
        
    },

    
}
</script>

