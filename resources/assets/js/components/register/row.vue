<template>
    <tr v-if="creating">
        <td v-if="index>=0">
            {{ index }}
        </td>
        <td v-if="can_select">
            <checkbox :value="student.user_id" :default="selected"
              @selected="onSelected"   @unselected="onUnselected">
               
            </checkbox>
           
        </td>
        <td v-if="can_edit">
            <button class="btn btn-danger btn-xs"
                @click.prevent="remove">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
        </td>
        
        <td v-text="student.signup.user.profile.fullname"></td> 
       
        <td>
           
           <span  v-if="can_select" :class="statusStyle(student.signup.status)">
           {{ statusText(student.signup.status) }}
           </span>

           <button v-else @click.prevent="onSelected" type="button" :class="statusStyle(student.signup.status)">
           {{ statusText(student.signup.status) }}
           </button>


        </td>
        <td v-text="student.signup.date"></td> 
        <td>{{ student.signup.tuition | formatMoney }}</td>  
        <td v-html="discountText(student.signup)"></td>
        <td v-if="show_updated">
            <updated :entity="student"></updated>
        </td>
    </tr>

    <tr v-else>
       <td v-if="can_edit">
            <button class="btn btn-danger btn-xs"
                @click.prevent="remove">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
       </td>
       <td v-text="student.number"></td>
       <td>
          <a @click.prevent="onStudentSelected">
            {{ student.user.profile.fullname  }}
          </a>
       </td>
       <td v-text="student.join_date"></td>
       <td v-html="activeLabel(student.active)"></td>
       <td v-text="student.user.phone"></td>
       <td v-text="student.user.email"></td>
       <td v-if="show_updated">
            <updated :entity="student"></updated>
       </td>
    </tr>
</template>

<script>
     
    export default {
        name: 'StudentRow',
        props: {
            creating:{
               type: Boolean,
               default: true
            },
            student: {
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
            activeLabel(val){
                return Student.activeLabel(val)
            },
            onSelected(){
                let user_id=this.student.user_id
                this.$emit('selected',user_id)
            },
            onUnselected(){
                let user_id=this.student.user_id
                this.$emit('unselected',user_id)
            },
            
            remove(id){
               let values={
                    name: this.student.user.profile.fullname,
                    id:this.student.id
                }
               this.$emit('remove',values)
            },
            onStudentSelected(){
               this.$emit('student-selected',this.student.id)
            }
          
        },

    }
</script>