<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4>
                <i class="fa fa-university" aria-hidden="true"></i>              
                所屬中心
                </h4>
            </span>
            
            <div>
                <button v-if="canCreate" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table class="table table-striped" style="width: 50%;">
             <thead> 
                <tr>                    
                    <th style="width:80%"></th> 
                    <th style="width:20%"></th>
                </tr> 
            </thead>
            <tbody> 
                <edit-admin-center v-for="center in admin.centers"  :center="center" 
                   :admin_id="id" :canDelete="center.canDelete"    @btnDeleteClicked="onBtnDeleteClicked" >
                </edit-admin-center>
                <edit-admin-center  :admin_id="id" :centers="admin.centersCanAdd"  v-if="creating"  @saved="onCreated"  @endEdit="endCreate" > </edit-Admin-center> 
            
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <modal :showBtn="true"  :show.sync="showConfirm" @ok="remove"  @closed="closeConfirm" ok-text="確定"
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
    import EditAdminCenter from '../../components/admin/edit-admin-center.vue'
    export default {
        name: 'AdminCenter',
        components: {
             'edit-admin-center':EditAdminCenter,
             'modal': Modal,
        },
        props: ['id'],
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                let centers=this.admin.centers
                if(!centers) return false    
                return this.admin.centers.length 
            },
            canCreate(){
                let centersCanAdd=this.admin.centersCanAdd
                if(!centersCanAdd) return false    
                return this.admin.centersCanAdd.length
            }
        },
        data() {
            return {

                loaded:false,
               
                creating:false,
                
                showConfirm:false,
                confirmMsg:'',
                deleteId:0,

                admin:{
                    centers:[],
                    centersCanAdd:[]
                },
             
            }
        },
        methods: {
            init() {
                this.loaded=false
               
                this.creating=false
                this.showConfirm=false
                this.admin={
                    centers:[],
                    centersCanAdd:[]
                }
                this.confirmMsg=''
                this.deleteId=0
               
                this.fetchData()
            }, 
            
            fetchData() {
                let url = '/api/centeradmin?admin=' + this.id
                axios.get(url)
                    .then(response => {
                       this.admin=response.data.admin
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
           
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            beginCreate(){
                this.creating=true

            },
            endCreate(){
                 this.creating=false
            },
            cancelEdit(){
               
               this.$emit('endEdittitle')
            },
            cancelCreate(){
               this.creating=false
               
            },
            onBtnDeleteClicked(values){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmRemove(values)
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
            },
            confirmRemove(values){
                this.confirmMsg='確定要刪除與 ' + values.name + ' 的關聯嗎？'
               
                this.deleteId=values.id

                this.showConfirm=true
            },
            closeConfirm(){
                this.showConfirm=false
            },
            remove(){
               let url = '/api/centeradmin/remove' 
                
                let form=new Form({
                    admin_id:this.id,
                    center_id:this.deleteId
                })
                form.post(url)
                .then(result => {
                    this.init()
                    Helper.BusEmitOK('刪除成功')
                   
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm();
                       
                }) 
            },

            onCreated(){    
                   this.init()
            },
            
            
           
        },

    }
</script>