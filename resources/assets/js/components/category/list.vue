<template>
    <div class="panel panel-default">
        <div  class="panel-heading">
            <div class="panel-title">
                <h4 v-html="title">
                </h4>
            </div>
            <div>
                <a @click="beginCreate" class="btn btn-primary">
                    新增課程分類
                </a>
                
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th> 分類名稱 </th>
                        <th> 代碼 </th>
                        <th> 類型</th>
                        <th> 小圖 </th>
                        <th> 狀態 </th>
                       
                        <th v-show="!edittingOrder">
                            順序
                            <button v-show="hasData" @click="beginEditOrder" class="btn btn-primary btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                            </button>
                        </th>
                        <th v-show="edittingOrder">
                            順序
                            <button @click="onSubmitDisplayOrders" class="btn btn-success btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-floppy-disk" ></span>
                            </button>
                            <button @click="cancelEditOrder" class="btn btn-default btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                   
                    <row  v-for="(category, index) in categories" :key="index"
                        :category="category" :index="index" :editting_order="edittingOrder"
                        @selected="onRowSelected" @clear-error="clearErrorMsg" >
                    </row>  
                   
                </tbody>  
            </table>
        </div>
    </div>

</template>

<script>
    import Row from './row.vue' 
    export default {
        name: 'CategoryList',
        components: {
            Row
        },
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
                title:Helper.getIcon(Category.title())  + '  課程分類',
                currenVersion:0,
                thead: [{
                        title: '分類名稱',
                        key: 'name',
                        sort: false,
                        default:true
                    }, 
                    {
                        title: '類型',
                        key: 'type',
                        sort: false,
                        default:true
                    },{
                        title: '小圖',
                        key: 'icon',
                        sort: false,
                        default:true
                    }, 
                    {
                        title: '狀態',
                        key: 'active',
                        sort: false,
                        default:true
                    }],
                
                edittingOrder: false,

                categories:[],
                canEdit:false
                
             
            }
        },
        computed: {
            hasData() {
              return this.categories.length > 0
            },
        },
        beforeMount() {
           this.init()
        },
        methods: {
            init() {
                this.edittingOrder=false
                this.categories=[]
                this.canEdit=false
                this.fetchData()
            },
            fetchData(){
                let getData=Category.index()
                getData.then(data => {
                    this.canEdit = data.canEdit
                    for(let i=0; i<data.categories.length ; i++){
	                    let category=data.categories[i]
	                    category.error=''
	                   
	                }
                    this.categories=data.categories

                    
                    this.loaded=true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            
            onSelected(id){
                this.$emit('selected',id)
            },
            clearErrorMsg(index){
                let category=this.categories[index]
                category.error= ''
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
            onRowSelected(id){
                this.$emit('selected',id)
            },
           
            onSubmitDisplayOrders(){
               this.hasError=true
               let errors=0
               for(let i=0; i<this.categories.length ; i++){
                    let category=this.categories[i]
                    let val=category.order
                    if(isNaN(val) || val.length < 1){
                          category.error= '必須為數字'
                          errors+=1
                    }else{
                        category.error= ''
                    }
                   
                }
              
                if(errors==0) {
                    this.updateDisplayOrder()
                }
            },
            updateDisplayOrder(){
               
                let form=new Form({
                    categories:this.categories
                })
                let save=Category.updateDisplayOrder(form)
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