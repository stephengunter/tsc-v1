<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-admin">
                <h4><i class="fa fa-key" aria-hidden="true"></i> 管理者資訊</h4>
            </span>
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table class="table table-striped" style="width: 75%;">
             <thead> 
                <tr> 
                    <th style="width:40%">角色</th> 
                    <th style="width:40%">最後更新</th> 
                    <th style="width:20%"></th>
                </tr> 
            </thead>
            <tbody> 
                <tr>
                    <td>
                        <role-label :labelstyle="admin.roleModel.style" 
                        :labeltext="admin.roleModel.display_name">                        
                        </role-label>
                    </td>
                    
                    <td v-if="!admin.updated_by" >{{   admin.updated_at|tpeTime  }}</td>
                     <td v-else>
                        <a  href="#" @click.prevent="showUpdatedBy">
                            {{   admin.updated_at|tpeTime  }}
                        </a>
                        
                     </td>
                    <td>
                        <button v-if="admin.canDelete"  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                        </button>
                    </td>
                </tr>
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteAdmin"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
          <div slot="modal-header" class="modal-header modal-header-danger">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="closeConfirm">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
             <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <h3 v-text="confirmMsg"> </h3>
        </div>
    </modal>



</div>

</template>

<script>
    import RoleLabel from '../../components/RoleLabel.vue'
    

    export default {
        props:['id'],
        name: 'Admin',
        components: {
            UserCard,
            RoleLabel,
            'modal': Modal,
        },
        beforeMount() {
           this.init()
        },
        
        data() {
            return {
                loaded:false,

                showConfirm:false,
                confirmMsg:'',

                admin:{},

                form:{},
             
            }
        },
        methods: {
            init() {
                this.loaded=false
              
                this.showConfirm=false

                this.admin={}
                this.confirmMsg=''  

                this.form={}  
               
                this.fetchData()
            }, 
            
            fetchData() {
                let url = '/api/admins/' + this.id  
                axios.get(url)
                    .then(response => {
                       this.admin=response.data.admin
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        
                        console.log(error)
                    })
            },
           
            btnDeleteClicked(values){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要刪除管理者資訊嗎？'
                    this.showConfirm=true
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
            },
            closeConfirm(){
                this.showConfirm=false
            },
            deleteAdmin(){
                let url = '/api/admins/' + this.id 

                let form=new Form()
                form.delete(url)
                .then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.backToIndex()
                })
                .catch(error => {
                                       
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm();
                       
                })
            },
            backToIndex() {
                this.$router.push('/admins')
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.admin.updated_by)
          }, 
           
        },

    }
</script>