<template>

<data-viewer :source="source" :thead="thead" :filter="filter"  :title="title" 
    :default_order="defaultOrder" :default_search="defaultSearch" :version="current_version"
     :create_text="createText" 
      @beginCreate="beginCreate" >
       
        <template scope="props">
            <tr>    
                <td><a herf="#" @click.prevent="onSelected(props.item.id)">{{props.item.name}}</a> </td>
                
                <td v-text="addressText(props.item.contactInfo)"></td>
                <td v-text="telText(props.item.contactInfo)"></td>
                <td v-text="faxText(props.item.contactInfo)"></td>   
                <td v-html="$options.filters.activeLabel(props.item.active)" ></td>
                <td>
                    <button @click="displayUp(props.item.id)" class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
                    </button>
                    <button @click="displayDown(props.item.id)" class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                    </button>
                </td>    
            </tr>
        </template>

</data-viewer>


</template>

<script>
     
    export default {
        name: 'CenterList',
        props: {
            hide_create:{
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
                current_version:0,
                defaultOrder:'display_order',
                defaultSearch:'name',
                
                createText:'新增開課中心',
                
                source: Center.source(),
                thead: [{
                        title: '名稱',
                        key: 'name',
                        sort: false,
                        default:true
                    }, {
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
                    }, {
                        title: '顯示順序',
                        key: 'display_order',
                        sort: false,
                        default:true
                    }],


                filter: [{
                            title: '名稱',
                            key: 'name',
                         }
                        ],

               

                
                selected: 0,
                
             
            }
        },
        beforeMount() {
           this.init()
        },
        methods: {
            init() {
                
                if(this.hide_create) this.createText=''
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
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            displayUp(id){
                 this.updateDisplayOrder(id,true)
            },
            displayDown(id){
                 this.updateDisplayOrder(id,false)
            },
            updateDisplayOrder(id,up){
                let update=Center.updateDisplayOrder(id,up)
                    update.then(data => {
                        this.current_version += 1
                    })
                    .catch(error => {
                        Helper.BusEmitError(error,'更新排序失敗')
                    })
            },
            
            
        },

    }
</script>