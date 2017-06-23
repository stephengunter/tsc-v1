<template>
<div>
    
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  :can_back="can_back"  
       :version="version"  @begin-edit="beginEdit" @loaded="onDataLoaded"
       @btn-back-clicked="onBtnBackClicked" >                 
    </show>

    <edit v-else :id="id" @edit-user="onEditUser"
       @saved="onSaved"   @canceled="onEditCanceled" >                 
    </edit> 

    
</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'
    export default {
        name:'StudentView',
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
            },      
            onDataLoaded(student){
                this.$emit('loaded',student)
            },        
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onEditUser(user_id){
                  this.$emit('edit-user', user_id )
            },
            onSaved(student){
                this.init()
                this.$emit('saved',student)
            },
            
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            
        }
    }
</script>
