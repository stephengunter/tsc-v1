<template>
<div>
     <list v-if="mode=='list'"  :signup_id="signup_id" :refund="refund" 
     :hide_create="hide_create" :can_select="can_select" 
      @selected="onSelected" @begin-create="onBeginCreate">
    </list>

    <create v-if="mode=='create'"  :signup_id="signup_id" :refund="refund" 
       @saved="onCreated" @canceled="onCreateCanceled">
    </create>

     <edit v-if="mode=='edit'"  :id="selected" :can_delete="true" :refund="refund" 
      @saved="onUpdated" @canceled="onEditCanceled" @deleted="onDeleted">
    </edit>
</div>    
</template>


<script>
    import List from '../../components/tuition/list.vue'
    import Create from '../../components/tuition/create.vue'
    import Edit from '../../components/tuition/edit.vue'

    export default {
        name: 'TuitionView',
        components: {
            List,
            Create,
            Edit
        },
        props: {
            signup_id: {
              type: Number,
              default: 0
            },
            refund: {
              type: Boolean,
              default: false
            },
            hide_create: {
              type: Boolean,
              default: false
            },
            can_select:{
               type: Boolean,
               default: true
            },
        },      
        data() {
            return {
                mode:'list',
                selected:0,
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                this.mode='list'
                this.selected=0
            },
            onSelected(id){
                this.selected=Helper.tryParseInt(id)
                this.mode='edit'
            },
            onBeginCreate(){
                this.mode='create'
            },
            onCreateCanceled(){
                this.init()
            },
            onCreated(){
                this.$emit('tuition-created')
                this.init()
            },
            onEditCanceled(){
                this.init()
            },
            onUpdated(){
               this.$emit('tuition-updated')
               this.init()
            },
            onDeleted(){
                this.$emit('tuition-deleted')
                this.init()
            }

            
           
            
            
        },

    }
</script>