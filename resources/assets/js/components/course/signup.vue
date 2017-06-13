<template>
<div>
 
   <show-signup   v-if="isReadOnly" @beginEditSignup="beginEdit" 
   :id="id"  :canEdit="canEdit">
   </show-signup>

   <edit-signup v-if="!isReadOnly" @saved="onSignupSaved" @endEditSignup="endEdit"
    :id="id" :canEdit="canEdit">        
    </edit-signup>
</div>
</template>
<script>
    import ShowSignup from '../../components/course/show-signup.vue'
    import EditSignup from '../../components/course/edit-signup.vue'
 
    export default {
        props: ['id', 'canEdit'],
        components: {
            'show-signup':ShowSignup,  
            'edit-signup':EditSignup,  
        },
        name: 'Signup',

        data() {
            return {
                isReadOnly:true,
            }
        }, 
        methods: {
            beginEdit() {
                 this.isReadOnly=false
            },
            endEdit(){
                 this.isReadOnly=true
            },
            onSignupSaved(){
                this.$emit('signupSaved')
                this.isReadOnly=true
            }
            
        }
    }
</script>
