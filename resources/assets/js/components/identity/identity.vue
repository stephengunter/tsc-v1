<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-identity">
                <h4><i class="fa fa-id-card-o" aria-hidden="true"></i> 身分管理</h4>
            </span>
            
            <div>
                <button class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 95%;">
             <thead> 
                <tr> 
                   
                    <th style="width:25%">名稱</th> 
                    <th style="width:20%">慈濟會員</th> 
                    <th style="width:35%">備註</th> 
                    <th style="width:20%"></th>
                </tr> 
            </thead>
            <tbody> 
                <edit-identity v-for="identity in identityList"  :identity="identity" 
                     @saved="identityUpdated"  @btnDeleteClicked="btnDeleteClicked" >
                </edit-identity>
                <edit-identity v-if="creating"  @saved="identityCreated"  @endEdit="endCreate" > </edit-identity> 
            
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteIdentity"  @closed="closeConfirm" ok-text="確定"
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
    import EditIdentity from '../../components/identity/edit-identity.vue'
    export default {
        name: 'identity',
        components: {
             'edit-identity':EditIdentity,
             'modal': Modal,
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.identityList.length) return true
                return false    
            }
        },
        data() {
            return {
                loaded:false,
                creating:false,
                identityList:[],
                showConfirm:false,
                confirmMsg:'',
                deleteId:0,
             
            }
        },
        methods: {
            init() {
                this.loaded=false

                this.creating=false
                this.showConfirm=false
                this.identityList=[]
                this.confirmMsg=''
                this.deleteId=''
               
                this.fetchData()
            }, 
            
            fetchData() {
                let url = '/api/identities'  
                axios.get(url)
                    .then(response => {
                      
                       this.identityList=response.data.identityList
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        
                        console.log(error)
                    })
            },
            doSearch(){
               this.fetchData()
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
               
               this.$emit('endEditIdentity')
            },
            cancelCreate(){
               this.creating=false
               
            },
            btnDeleteClicked(values){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要刪除 ' + values.name + ' 嗎？'
                    this.deleteId=values.id
                    this.showConfirm=true
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
                
            },
            closeConfirm(){
                this.showConfirm=false
            },
            deleteIdentity(){
                let url = '/api/identities/' + this.deleteId 
                let form=new Form()
                form.delete(url)
                .then(result => {
                    this.init()
                    Helper.BusEmitOK('刪除成功') 

                    this.deleteId=0;
                    this.closeConfirm();
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')

                    this.closeConfirm();
                       
                })
            },
            identityCreated(identity){    
                   this.init()
            },
            identityUpdated(identity){ 
                  this.init()
            },
            
           
        },

    }
</script>