<template>
<div>
    
    <show v-if="readOnly"  :id="id" can_edit="can_edit"  
       :version="version"  @begin-edit="beginEdit" @loaded="onDataLoaded"
      >                 
    </show>

    <edit v-else :id="id" 
       @saved="onSaved"   @canceled="onEditCanceled" >                 
    </edit> 

  
</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'


    export default {
        name:'SignupInfoView',
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
            onDataLoaded(signupinfo){
                this.$emit('loaded',signupinfo)
            },        
            beginEdit() {
                this.readOnly=false
            },
            onEditCanceled(){
                this.init()
            },
            onSaved(signupinfo){
                this.init()
                this.$emit('saved',signupinfo)
            },
            
           
            
            
        }
    }
</script>
