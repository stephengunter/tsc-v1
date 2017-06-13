<template>
<div>
     <list v-if="mode=='list'"  :course_id="course_id" :user_id="user_id" 
     :hide_create="hide_create" :can_select="can_select" 
      @selected="onSelected" @begin-create="onBeginCreate">
    </list>

    <create v-if="mode=='create'"  :course_id="course_id" :user_id="user_id" 
      @saved="onCreated" @canceled="onCreateCanceled">
    </create>

    <edit v-if="mode=='edit'"  :id="selected" 
      @saved="onUpdated" @canceled="onEditCanceled">
    </edit>
</div>    
</template>


<script>
    import List from '../../components/signup/list.vue'
    import Create from '../../components/signup/create.vue'
    import Edit from '../../components/signup/edit.vue'

    export default {
        name: 'SignupView',
        components: {
            List,
            Create,
            Edit
        },
        props: {
            course_id: {
              type: Number,
              default: 0
            },
            user_id: {
              type: Number,
              default: 0
            },
            hide_create: {
              type: Boolean,
              default: false
            },
            can_select:{
               type: Boolean,
               default: false
            },
            disable_edit:{
               type: Boolean,
               default: false
            }
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
                if(!this.disable_edit){
                   this.mode='edit'
                }else{
                   this.$emit('selected',id)
                }
                
            },
            onBeginCreate(){
                this.mode='create'
            },
            onCreateCanceled(){
                this.init()
            },
            onCreated(){
                this.init()
            },
            onEditCanceled(){
                this.init()
            },
            onUpdated(){
               this.init()
            },

            
           
            
            
        },

    }
</script>