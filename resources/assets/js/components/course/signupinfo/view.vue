<template>
<div>
    
    <show v-if="readOnly"  :signupinfo="signupinfo" :can_edit="can_edit"  
         @begin-edit="beginEdit"  >       
               
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
            course: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },   
        },
        data() {
            return {
                readOnly:true,
                signupinfo:null,
            }
        },
        computed: {
            id() {
               if(this.course) return this.course.id
                return 0
            },
        },
        beforeMount(){
            this.init()
        },
        watch:{
            course: {
              handler: function () {
                  this.signupinfo=new SignupInfo(this.course)
              },
             
            },            
        },
        methods: {
            init() {
               this.readOnly=true
               this.signupinfo=new SignupInfo(this.course)
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
