<template>

<div>
     
    <data-viewer :source="source" :defaultOrder="defaultOrder" :defaultSearch="defaultSearch"
       :version="version"  :thead="thead" :filter="null" @beginCreate="beginCreate"
        :title="title" showCreateBtn="false" createText="新增課程分類">
       
        <template scope="props">
            <tr>    
               
                <td><a herf="#" @click="details(props.item.id)">{{props.item.name}}</a> </td>
                <td v-html="$options.filters.showIcon(props.item.icon)"></td> 
                <td v-html="$options.filters.activeLabel(props.item.active)"></td> 

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

<div>
</template>
    <script>
        

        export default {
            name: 'CenterIndex',
            components: {
                DataViewer,              
            },
            data() {
                return {
                    title: 'Categories',
                    source: '/api/categories',
                    defaultOrder:'order',
                    defaultSearch:'name',
                    version:0,
                    
                    thead: [{
                        title: '分類名稱',
                        key: 'name',
                        sort: false,
                        default:true
                    }, {
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
                    }, {
                        title: '顯示順序',
                        key: 'order',
                        sort: false,
                        default:true
                    }],

                   
                   
                   
                }
            },
            methods: {
                beginCreate(){
                    this.$router.push('/categories/create')  
                },
                details(id){
                    this.$router.push('/categories/' + id)
                },
                displayUp(id){
                   this.updateDisplayOrder(id,true)
                },
                displayDown(id){
                     this.updateDisplayOrder(id,false)
                },
                updateDisplayOrder(id,up){
                    let form = new Form({                        
                         up: up
                    })
                    
                    let url = '/api/categories/' + id + '/displayOrder'

                    form.put(url)
                    .then(category => {
                       this.version+=1
                    })
                    .catch(error => {
                        Helper.BusEmitError(error,'更新排序失敗')
                        
                    })
                },
                
            },

            
        }
    </script>