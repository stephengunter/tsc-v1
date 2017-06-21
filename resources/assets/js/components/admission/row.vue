<template>
    <tr>
        <td v-if="can_select">
            <button @click.prevent="selected(admit.signup_id)"  type="button" class="btn-xs btn btn-primary">
                選取
            </button>
        </td>
        <td v-if="can_edit">
            <button class="btn btn-danger btn-xs"
                @click.prevent="remove(admit.id)">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
        </td>
        <td v-text="admit.display_order"></td> 
        <td v-text="admit.signup.user.profile.fullname"></td> 
       
        <td>
           <button @click.prevent="selected(admit.id)" type="button" :class="statusStyle(admit.status)">
           {{ statusText(admit.signup.status) }}
           </button>
        </td>
        <td v-text="admit.signup.date"></td> 
        <td>{{ admit.signup.tuition | formatMoney }}</td>  
        <td v-html="discountText(admit.signup)"></td>
        <td>
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
        },
        data() {
            return {
                
             
            }
        },
        
        methods: {
            
            statusStyle(status){
                return 'btn-xs btn btn-' + Signup.getStatusStyle(status)
            },
            statusText(status){
                return Signup.getStatusText(status)
            },
            discountText(signup){
                if(!signup.discount) return ''
                return Signup.formatDiscountText(signup.discount, signup.points)
            },
            selected(id){
                this.$emit('selected',id)
            },
            
            
            remove(id){
              alert(id)
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