<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-id-card-o" aria-hidden="true"></i> 稱謂管理</h4>
            </span>
            
            <div>
                <button class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 50%;">
             <thead> 
                <tr> 
                   
                    <th style="width:80%">名稱</th> 
                    <th style="width:20%"></th>
                </tr> 
            </thead>
            <tbody> 
                <edit-title v-for="title in titleList"  :title="title" 
                     @saved="titleUpdated"  @btnDeleteClicked="btnDeleteClicked" >
                </edit-title>
                <edit-title v-if="creating"  @saved="titleCreated"  @endEdit="endCreate" > </edit-title> 
            
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteTitle"  @closed="closeConfirm" ok-text="確定"
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
    import EditTitle from '../../components/title/edit-title.vue'
    export default {
        name: 'title',
        components: {
             'edit-title':EditTitle,
             'modal': Modal,
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.titleList.length) return true
                return false    
            }
        },
        data() {
            return {
                loaded:false,
                creating:false,
                titleList:[],
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
                this.titleList=[]
                this.confirmMsg=''
                this.deleteId=''
               
                this.fetchData()
            }, 
            
            fetchData() {
                let url = '/api/titles'  
                axios.get(url)
                    .then(response => {
                      
                       this.titleList=response.data.titleList
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
               
               this.$emit('endEdittitle')
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
            deleteTitle(){
                let url = '/api/titles/' + this.deleteId 
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
            titleCreated(title){    
                   this.init()
            },
            titleUpdated(title){ 
                  this.init()
            },
            
           
        },

    }
</script>