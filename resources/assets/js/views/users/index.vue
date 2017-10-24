<template>
<div>
      
    
    
    <user-list :hide_create="hide_create" :version="version"
      @selected="userSelected" @add-to-role="addToRole"
      @begin-create="onBeginCreate">
    </user-list>

    

</div>

</template>
<script>
    import UserList from '../../components/user/list.vue'

    export default {
        name: 'UserIndex',
        props: {
            hide_create: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            }
        },
        components: {
            UserList
        },
        data() {
            return {
                loaded:false,
            }
        },
        methods: {
            
            
            beginCreate(){
                this.$emit('beginCreate')
            },
            userSelected(id){
                
                this.$emit('selected',id) 
            },
            addToRole(user_id,role){
                this.$emit('add-to-role',user_id,role) 
            },
            onBeginCreate(){
                 this.$emit('begin-create') 
            }


        },

        
    }
</script>