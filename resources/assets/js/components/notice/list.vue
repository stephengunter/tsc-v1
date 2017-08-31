<template>
    <data-viewer  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source"  :thead="thead" 
      :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
     
         
         
         <template scope="props">
            <tr>
                <td><a herf="#" @click="selected(props.item.id)">{{ props.item.title }}</a> </td>
                <td v-text="textContent(props.item.content)"></td>
                <td v-html="$options.filters.okSign(props.item.public)" ></td> 
                <td  v-html="$options.filters.okSign(props.item.emails)" ></td> 
               
             
                </td>
                
                <td>{{ props.item.created_at | strTime }}</td>  
            </tr>
        </template>

    </data-viewer>

</template>

<script>
     
    export default {
        name: 'NoticeList',
        props: {
            hide_create: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            },
        },
        beforeMount() {
           this.init()
        },
        data() {
            return {
                title:Helper.getIcon(Notice.title())  + '  公告管理',
                loaded:false,
                source: Notice.source(),
                
                defaultSearch:'title',
                defaultOrder:'created_at',                
                create: Notice.createUrl(),
                
                thead:[],
                filter: [
                         {
                            title: '標題',
                            key: 'title',
                         },
                         {
                            title: '日期',
                            key: 'created_at',
                         }
                        ],
              
                viewMore:false
             
            }
        },
        computed: {
            createText(){
                if(this.hide_create) return ''
                return '新增公告'
            },
        },
        methods: {
            init() {
                this.thead=Notice.getThead()
            },
            textContent(val){
               let text= Helper.removeHTML(val)
               if(text.length>50){
                  return text.substr(0,50) + '...'
               }else{
                  return text
               }
               
            },  
               
            onDataLoaded(data){
               
            }, 
            
            selected(id){
                this.$emit('selected',id)
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            
           
        },

    }
</script>