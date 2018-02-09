<template>

    <tr>
      
       <td>
          <a @click.prevent="onStudentSelected">
            {{ student.user.profile.fullname  }}
          </a>
       </td>
       <td>
           {{  student.identityText }}
            <span v-if="isTrue(student.confirmed)">
              <i class="fa fa-check-circle" style="color:green" aria-hidden="true"></i>
           </span>
       </td>
       <td v-text="student.join_date"></td>
       <td v-text="student.user.phone"></td>
       <td v-text="student.user.email"></td>
       <td v-html="activeLabel(student.active)"></td>
    </tr>
</template>

<script>
     
    export default {
        name: 'StudentRow',
        props: {
            
            student: {
              type: Object,
              default: null
            },
            index: {
               type: Number,
               default: -1
            },
        },
        data() {
            return {
                
             
            }
        },
        
        methods: {
            isTrue(val){
                return Helper.isTrue(val)
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