<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
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
                <edit v-for="identity in identityList"  :identity="identity" @canceled="onEditCanceled"
                     @saved="onUpdated"  @btn-delete-clicked="onBtnDeleteClicked" >
                </edit>
                <edit v-if="creating"  @saved="onCreated"  @canceled="onCreateCanceled" > </edit> 
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteIdentity">        
    </delete-confirm>       

    

</div>

</template>

<script>
    import Edit from '../../components/identity/edit.vue'
    export default {
        name: 'IdentityIndex',
        components: {
             Edit,
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
              
                orderOptions:{},

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
             
            }
        },
        methods: {
            init() {
                this.loaded=false
                this.creating=false
              
                this.identityList=[]
                
                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:'',

                }
                
                this.fetchData()         
            }, 
            fetchData() {
                let index=Identity.index()
                index.then(data => {
                   
                   this.identityList=data.identityList
                   this.loaded = true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            onEditCanceled(){
                this.init()
            },
            beginCreate(){
                this.creating=true
            },
            onCreateCanceled(){
                 this.init()
            },
            onBtnDeleteClicked(values){
                this.deleteConfirm.msg='確定要刪除身分： ' + values.name + ' 嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true     
                
            },
            onCreated(){    
                this.init()
                this.$emit('created')
            },
            onUpdated(){
                this.init()
                this.$emit('updated')
            },            
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteIdentity(){
                let id = this.deleteConfirm.id 
                let remove= Identity.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.init()
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },
            
           
        },

    }
</script>