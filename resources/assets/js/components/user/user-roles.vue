<template>
    <modal :show.sync="showing" @closed="onClose" @ok="submitRole" 
         title="加入角色" effect="fade" width="800">
        
        <div slot="modal-body" class="modal-body">
            <p style="font-size:17px" >請選擇您要加入的角色</p>

            <toggle  :items="roleOptions"  
                :default_val="selectedRole" @selected="onRoleSelected">
                    
            </toggle>
            
        </div>
    </modal>
</template>



<script>
    
    export default {
        name: 'UserRoles',
        props: {
            user_id:{
               type: Number,
               default: 0
            },
            showing:{
               type: Boolean,
               default: true
            },
            is_create:{
                type: Boolean,
                default: true
            } 
        },
        data(){
            return {
               roleOptions:[],
               selectedRole:'',     
            }
        },
        watch: {
            showing: function () {
                if(this.showing){
                    this.init()
                }
            }
        },
        methods: {
            init(){
                this.selectedRole=''
                this.fetchData()
            },
            fetchData() {
                if(!this.user_id) return false
                axios.get('/user-roles/' + this.user_id + '/edit')
                    .then(response => {
                        let roles = response.data.roles
                        let options = []
                        for (let i = 0; i < roles.length; i++) {

                            let option = {
                                text: roles[i].display_name,
                                value: roles[i].name
                            }

                            options[i] = option
                        }

                        this.roleOptions = options
                        this.selectedRole = options[0].value
                       

                    })
                    .catch(error=> {
                        Helper.BusEmitError(error)
                   })

            },
            onClose(){
                this.$emit('canceled')
            },
            onRoleSelected(role){
                this.selectedRole=role
               
            },
            submitRole(){

                 let url=''
                 switch (this.selectedRole) {
                     case 'Teacher':
                        url = Teacher.createUrl()
                     break;
                     case 'Volunteer':
                        url = Volunteer.createUrl()
                     break;
                     case 'Admin':
                        url = Admin.createUrl()
                     break;
                      case 'Owner':
                        url = Admin.createUrl()
                     break;
                 }

                 url += '?user=' + this.user_id
                 Helper.redirect(url)
                 
            }
            
        }
     }
</script>