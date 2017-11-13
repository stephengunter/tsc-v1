<template>
    <div class="panel panel-default">
        <div  class="panel-heading">
            <div class="panel-title">
                 <h4><i class="fa fa fa-download" aria-hidden="true"></i> 文件下載管理</h4>
            </div>
            <div>
                <a @click="beginCreate" class="btn btn-primary">
                    新增文件下載
                </a>
                
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:30%;"> 標題 </th>
                        <th> 檔案名稱 </th>
                        
                        
                       
                        <th v-show="!edittingOrder">
                            順序
                            <button v-show="hasData" @click="beginEditOrder" class="btn btn-primary btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                            </button>
                        </th>
                        <th style="width:20%;" v-show="edittingOrder">
                            順序
                            <button @click="onSubmitDisplayOrders" class="btn btn-success btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-floppy-disk" ></span>
                            </button>
                            <button @click="cancelEditOrder" class="btn btn-default btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                            </button>
                        </th>
                        <th style="width:10%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(download,index) in downloads" :key="index">
                        <td >
                            <a href="#" @click.prevent="onSelected(download.id)">{{ download.title}}</a> 
                        </td>
                        <td v-text="download.name"></td>
                        
                        <td v-if="edittingOrder">
                            <input @keydown="clearErrorMsg(index)" type="text" name="download.order" class="form-control" v-model="download.order">
                
                            <small class="text-danger" v-text="download.error"></small>
                        </td> 
                        <td v-else v-text="download.order"></td>
                        <td>
                            <button v-if="download.canDelete"  class="btn btn-danger btn-xs"
                                @click.prevent="btnDeleteClicked(download)">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                            </button>
                        </td>
                    </tr>
                    
                   
                </tbody>  
            </table>
        </div>
    </div>

</template>

<script>
    
    export default {
        name: 'DownloadList',
        props: {
            can_create:{
               type: Boolean,
               default: false
            },
            can_edit:{
               type: Boolean,
               default: false
            },
            version:{
               type: Number,
               default: 0
            }
        },
        watch: {
            version() {
                this.fetchData()               
            }
        },
        data() {
            return {
                
                edittingOrder: false,

                downloads:[],
                canEdit:false
                
             
            }
        },
        computed: {
            hasData() {
              return this.downloads.length > 0
            },
        },
        beforeMount() {
           this.init()
        },
        methods: {
            init() {
                this.edittingOrder=false
                this.downloads=[]
                this.canEdit=false
                this.fetchData()
            },
            fetchData(){
                let getData=Download.index()
                getData.then(data => {
                    this.canEdit = data.canEdit
                    for(let i=0; i<data.downloads.length ; i++){
	                    let download=data.downloads[i]
	                    download.error=''
	                   
	                }
                    this.downloads=data.downloads

                    
                    this.loaded=true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            
            onSelected(id){
                this.$emit('selected',id)
            },
            btnDeleteClicked(download){
                this.$emit('delete',download)
            },
            clearErrorMsg(index){
               
                let download=this.downloads[index]
                download.error= ''
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            beginEditOrder(){
                this.edittingOrder=true
            },
            cancelEditOrder(){
                 this.init()
            },
            
            onSubmitDisplayOrders(){
                
                let errors=0
                for(let i=0; i<this.downloads.length ; i++){
                    let download=this.downloads[i]
                    let val=download.order
                    if(isNaN(val) || val.length < 1){
                          download.error= '必須為數字'
                          errors+=1
                    }else{
                        download.error= ''
                    }
                   
                }
              
                if(errors==0) {
                    this.updateDisplayOrder()
                }
            },
            updateDisplayOrder(){
                 Helper.BusEmitOK()
                        this.init()

                        return
                let downloads = this.downloads.map(item=>{
                    return {
                                id:item.id,
                                order:item.order
                          }
                })

                
                let save=Download.updateDisplayOrder(downloads)
                save.then(result => {
                        Helper.BusEmitOK()
                        this.init()
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            },
            
            
        },

    }
</script>