<template>
    <div class="panel panel-default">
        <div  class="panel-heading">
            <div class="panel-title">
                <h4 v-html="title">
                </h4>
            </div>
            <div>
                
                <a @click="beginCreate" class="btn btn-primary">
                    新增開課中心
                </a>
                
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                         
                        <th v-for="(item,index) in thead" :key="index" >
                            {{item.title}}
                        </th>
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
                    <tr v-for="(center, index) in centers" :key="center.id">    
                        <td>
                            <a herf="#" @click.prevent="onSelected(center.id)"> {{center.name}}
                            </a> 
                        </td>
                        <td>{{ center.code }}</td>
                        <td v-text="addressText(center.contactInfo)"></td>
                        <td v-text="telText(center.contactInfo)"></td>
                        <td v-text="faxText(center.contactInfo)"></td>   
                        <td v-html="$options.filters.activeLabel(center.active)" ></td>
                        
                        <td v-show="!edittingOrder" v-text="center.display_order">
                            
                        </td> 
                        <td v-show="edittingOrder">
                            <input @keydown="clearErrorMsg(index)" type="text" name="center.display_order" class="form-control" v-model="center.display_order">
                  
                            <small class="text-danger" v-text="center.error"></small>
                        </td>   
                    </tr>
                </tbody>  
            </table>
        </div>
    </div>

</template>

<script>
     
    export default {
        name: 'CenterList',
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
            version: function () {
               this.current_version += 1
            }
        },
        data() {
            return {
                title:Helper.getIcon('Centers')  + '  開課中心管理',
                
                thead: [{
                        title: '名稱',
                        key: 'name',
                        sort: false,
                        default:true
                    }, {
                        title: '代碼',
                        key: 'code',
                        sort: false,
                        default:true
                    },{
                        title: '地址',
                        key: 'name',
                        sort: false,
                        default:true
                    }, {
                        title: '電話',
                        key: 'name',
                        sort: false,
                        default:true
                    }, {
                        title: '傳真',
                        key: 'name',
                        sort: false,
                        default:true

                    }, {
                        title: '狀態',
                        key: 'active',
                        sort: false,
                        default:true
                    }],
                
                edittingOrder: false,

                centers:[]
                
             
            }
        },
        computed: {
            hasData() {
              return this.centers.length > 0
            },
        },
        beforeMount() {
           this.init()
        },
        methods: {
            init() {
                this.edittingOrder=false
                this.centers=[]

              

                this.fetchData()
            },
            fetchData(){
                let getData=Center.index()
                getData.then(data => {
                    let centers = data.centers
                    this.centers=centers

                    
                    this.loaded=true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            addressText(contactInfo) {
                if(!contactInfo) return ''
                if(!contactInfo.addressA) return ''
                return contactInfo.addressA.fullText
            },
            telText(contactInfo) {
                if(!contactInfo) return ''
                return contactInfo.tel
            },
            faxText(contactInfo){
                if(!contactInfo) return ''
                return contactInfo.fax
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            clearErrorMsg(index){
                let center=this.centers[index]
                center.error= ''
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
               this.hasError=true
               let errors=0
               for(let i=0; i<this.centers.length ; i++){
                    let center=this.centers[i]
                    let val=center.display_order
                    if(isNaN(val) || val.length < 1){
                          center.error= '必須為數字'
                          errors+=1
                    }else{
                        center.error= ''
                    }
                   
                }
              
                if(errors==0) {
                    this.updateDisplayOrder()
                }
            },
            updateDisplayOrder(){
               
                let form=new Form({
                    centers:this.centers
                })
                let save=Center.updateDisplayOrder(form)
                save.then(result => {
                        Helper.BusEmitOK()
                        this.init()
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            },
            resetImport(){
               this.$refs.fileinput.value = null
            },
            onFileChange(e) {
              
                var files = e.target.files || e.dataTransfer.files
                if (!files.length)  return
                   
                this.files = e.target.files

                this.submitImport()
            },
            submitImport() {
                let type=1
                if(!Helper.isTrue(this.type)) type=0

                let form = new FormData()
                for (let i = 0; i < this.files.length; i++) {
                    form.append('teachers_file', this.files[i])
                    form.append('type', type)
                }

                let store=Teacher.import(form)
                store.then(result => {
                        // Helper.BusEmitOK()
                        // this.$emit('saved')  
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            },
            
            
        },

    }
</script>