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
            <table v-show="hasData" class="table table-striped" style="width: 95%;">
             <thead> 
                <tr> 
                    <th>名稱</th> 
                    <th></th>                     
                </tr> 
            </thead>
            <tbody> 
                <edit v-for="title in titleList"  :title="title" @canceled="onEditCanceled"
                     @saved="onUpdated"  @btn-delete-clicked="onBtnDeleteClicked" >
                </edit>
                <edit v-if="creating"  @saved="onCreated"  @canceled="onCreateCanceled" > </edit> 
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteTitle">        
    </delete-confirm>       

    

</div>

</template>

<script>
    import Edit from '../../components/title/edit.vue'
    export default {
        name: 'TitleIndex',
        components: {
             Edit,
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
              
                this.titleList=[]
                
                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:'',

                }
                
                this.fetchData()         
            }, 
            fetchData() {
                let index=Title.index()
                index.then(data => {
                   
                   this.titleList=data.titleList
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
                this.deleteConfirm.msg='確定要刪除 ' + values.name + ' 嗎？'
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
            deleteTitle(){
                let id = this.deleteConfirm.id 
                let remove= Title.delete(id)
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