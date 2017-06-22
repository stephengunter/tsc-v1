<template>
    <tr>
        <td v-if="index>=0">
            {{ index }}
        </td>
        <td v-if="can_select">
            <checkbox :value="admit.signup.id" :default="selected"
              @selected="onSelected"   @unselected="onUnselected">
               
            </checkbox>
        </td>
        <td v-if="can_edit">
            <button class="btn btn-danger btn-xs"
                @click.prevent="remove(admit.id)">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
        </td>
        
        <td v-text="admit.signup.user.profile.fullname"></td> 
       
        <td>
           
           <span  v-if="can_select" :class="statusStyle(admit.signup.status)">
           {{ statusText(admit.signup.status) }}
           </span>

           <button v-else @click.prevent="onSelected" type="button" :class="statusStyle(admit.signup.status)">
           {{ statusText(admit.signup.status) }}
           </button>


        </td>
        <td v-text="admit.signup.date"></td> 
        <td>{{ admit.signup.tuition | formatMoney }}</td>  
        <td v-html="discountText(admit.signup)"></td>
        <td v-if="show_updated">
            <updated :entity="admit"></updated>
        </td>
    </tr>
</template>

<script>
     
    export default {
        name: 'AdmitRow',
        props: {
            admit: {
              type: Object,
              default: null
            },
            show_updated: {
              type: Boolean,
               default: true
            },
            hide_create: {
              type: Boolean,
              default: false
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            can_select:{
               type: Boolean,
               default: false
            },
            index: {
               type: Number,
               default: -1
            },
            selected:{
               type: Boolean,
               default: false
            }
        },
        data() {
            return {
                
             
            }
        },
        
        methods: {
            statusStyle(status){
              if(this.can_select){
                 return 'label label-' + Signup.getStatusStyle(status)
              }else{
                return 'btn-xs btn btn-' +  Signup.getStatusStyle(status)
              }
             
            },
            statusText(status){
                return Signup.getStatusText(status)
            },
            discountText(signup){
                if(!signup.discount) return ''
                return Signup.formatDiscountText(signup.discount, signup.points)
            },
            onSelected(){
                let signup_id=this.admit.signup_id
                this.$emit('selected',signup_id)
            },
            onUnselected(){
                let signup_id=this.admit.signup_id
                this.$emit('unselected',signup_id)
            },
            
            remove(id){
               this.$emit('remove',id)
            },
            btnDeleteClicked(){
                 let values={
                    // name: this.category.name,
                    // id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
           
        },

    }
</script>