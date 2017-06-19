<template>
    <data-viewer v-if="loaded"  :default_search="defaultSearch" 
      :default_order="defaultOrder"  :default_direction="defaultDirection"
      :source="source"   :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" :create_text="createText" 
      :no_page="no_page"
      @refresh="init" :version="current_version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
         
        
        
        <template scope="props">
            <row :category="props.item" 
               @selected="onRowSelected"
               @display-up="onDisplayUp" @display-down="onDisplayDown">
                
            </row>
        </template>

    </data-viewer>

</template>

<script>
    import Row from './row.vue'
    export default {
        name: 'CategoryList',
        components: {
            Row
        },
        props: {
            hide_create: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            },
            can_select:{
               type: Boolean,
               default: true
            },
            no_page:{
               type: Boolean,
               default: true
            },
        },
        beforeMount() {
           this.init()
        },
        data() {
            return {
                title:Helper.getIcon(Category.title())  + '  課程分類',
                loaded:false,
                source: Category.source(),
                
                defaultSearch:'name',
                defaultOrder:'order',   
                defaultDirection:'desc',   

                create: Category.createUrl(),
                
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
                    }, {
                        title: '顯示順序',
                        key: 'order',
                        sort: false,
                        default:true
                    }],
               

                filter:[],

                current_version:0,

                hasData:false,
             
            }
        },
        watch: {
            version: function () {
               this.current_version += 1
            }
        },
        computed: {
            createText(){
                if(this.hide_create) return ''
                return '新增分類'
            },
        },
        methods: {
            init() {
                this.loaded=true
            },

            onDataLoaded(data){
                this.hasData=data.model.total
            },
            onRowSelected(id){
                this.$emit('selected',id)
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            onDisplayUp(id){
                this.updateDisplayOrder(id,true)
            },  
            onDisplayDown(id){
                this.updateDisplayOrder(id,false)
            },
            updateDisplayOrder(id,up){
                let update=Category.updateDisplayOrder(id,up) 
              
                update.then(data => {
                   this.current_version += 1                           
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
           
        },

    }
</script>