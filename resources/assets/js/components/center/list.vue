<template>
    <div class="panel panel-default">
        <div  class="panel-heading">
            <div class="panel-title">
                <h4 v-html="title">
                </h4>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <select @change="onParamChanged"  v-model="params.oversea" style="width:auto;" class="form-control selectWidth">
                        <option v-for="(item,index) in overseaOptions" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div v-show="!isOversea" class="form-group">
                    <select  @change="onParamChanged"  v-model="params.area" style="width:auto;" class="form-control selectWidth">
                        <option v-for="(item,index) in area_options" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
            </div>
            <div>
                
                <a v-if="false" @click="beginCreate" class="btn btn-primary">
                    新增開課中心
                </a>
                
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>名稱</th>
                        <th>代碼</th>
                        <th style="width:25%">地址</th>
                        <th v-if="!isOversea">課程洽詢電話</th>
                        <th>電話</th>
                        <th>傳真</th>
                        <th>狀態</th>
                        
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
                            <a href="#" @click.prevent="onSelected(center.id)"> {{center.name}}
                            </a> 
                        </td>
                        <td>{{ center.code }}</td>
                        <td v-text="addressText(center.contactInfo)"></td>

                        <td v-if="!isOversea" v-text="center.course_tel"></td>

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
            area_options:{
               type: Array,
               default: null
            },
            version:{
               type: Number,
               default: 0
            }
        },
        watch: {
            version () {
               this.init()
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
                        title: '課程洽詢電話',
                        key: 'course_tel',
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
                overseaOptions:Center.overseaOptions() ,
                params:{
                    oversea:0,
                    area:0
                },
                centers:[]
                
             
            }
        },
        computed: {
            hasData() {
              return this.centers.length > 0
            },
            isOversea(){
              return  parseInt(this.params.oversea) > 0
            }
        },
        beforeMount() {
            if(!this.area_options || !this.area_options.length)  return false
            
            this.params.area=this.area_options[0].value
            this.init()
        },
        methods: {
            init() {
                
                this.edittingOrder=false
                this.centers=[]

                this.fetchData()
            },
            fetchData(){
                
                let getData=Center.index(this.params.oversea,this.params.area)
                getData.then(data => {
                    let centers = data.centers
                    this.centers=centers

                    
                    this.loaded=true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            onParamChanged(){
                this.fetchData()
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
                for(let i=0; i < this.centers.length ; i++){
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
               
                let centers = this.centers.map(item=>{
                    return {
                                id:item.id,
                                order:item.display_order
                          }
                })
                
                let save=Center.updateDisplayOrder(centers)
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