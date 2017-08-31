<template>
    <tr>
       <td v-text="student.number"></td>
       <td v-text="student.name"></td>
       <td v-text="student.score.points"></td>
       <td>
         <button v-if="can_edit" v-show="student.canEdit"  class="btn btn-primary btn-xs" 
            @click.prevent="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
         </button> 
       </td>
    </tr>
</template>

<script>
     
    export default {
        name: 'ScoreRow',
        props: {
            student: {
              type: Object,
              default: null
            },
            show_updated: {
               type: Boolean,
               default: true
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
            beginEdit(){

            },
            
            statusText(status){
                return Signup.getStatusText(status)
            },
            discountText(signup){
                if(!signup.discount) return ''
                return Signup.formatDiscountText(signup.discount, signup.points)
            },
            activeLabel(val){
                return Score.activeLabel(val)
            },
            onSelected(){
                let user_id=this.score.user_id
                this.$emit('selected',user_id)
            },
            onUnselected(){
                let user_id=this.score.user_id
                this.$emit('unselected',user_id)
            },
            
            remove(id){
               let values={
                    name: this.score.user.profile.fullname,
                    id:this.score.id
                }
               this.$emit('remove',values)
            },
            onScoreSelected(){
               this.$emit('score-selected',this.score.id)
            }
          
        },

    }
</script>