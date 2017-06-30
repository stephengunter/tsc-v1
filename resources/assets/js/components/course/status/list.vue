<template>
    <data-viewer  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="search_params"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" create_text="" :no_page="no_page"
      @refresh="init" :version="version"   
       @dataLoaded="onDataLoaded">
         
         <template scope="props">
            <row :course="props.item"  :select="can_select"
               @selected="onRowSelected">
                
            </row>
            
        </template>

    </data-viewer>

</template>

<script>
    
    import Row from './row.vue'
    export default {
        components: {
            Row
        },
        name: 'CourseStatusList',
        props: {
            search_params: {
              type: Object,
              default: null
            },
            hide_create: {
              type: Boolean,
              default: true
            },
            version:{
               type: Number,
               default: 0
            },
            can_select:{
               type: Boolean,
               default: true
            },
        },
        beforeMount() {
           this.init()
        },
        watch: {
            course_id: function (value) {
               this.searchParams.course=value
            }
        },
        data() {
            return {
                title:Helper.getIcon(CourseStatus.title())  + '  課程狀態查詢',
                loaded:false,
                source: CourseStatus.source(),

                no_page:true,
                defaultSearch:'name',
                defaultOrder:'open_date', 

                thead:[{
                    title: '',
                    key: 'name',
                    sort: false,
                    static:true,
                    default:true

                },{
                    title: '課程日期',
                    key: 'begin_date',
                    sort: true,
                    default:true

                }, {
                    title: '報名日期',
                    key: 'open_date',
                    sort: true,
                    default:true
                }, {
                    title: '審核',
                    key: 'reviewed',
                    sort: true,
                    default:true
                }, {
                    title: '上架',
                    key: 'active',
                    sort: true,
                    default:true
                },{
                    title: '報名',
                    key: 'status.signup',
                    sort: false,
                    default:true
                },{
                    title: '註冊',
                    key: 'status.register',
                    sort: false,
                    default:true
                },{
                    title: '開課',
                    key: 'status.class',
                    sort: false,
                    default:true
                }],

                filter: [{
                    title: '名稱',
                    key: 'name',
                }],
  
                
                hasData:false,
             
            }
        },
        
        methods: {
            init() {
              
            },
            onDataLoaded(data){
                this.hasData=data.model.total
            },
            onBtnViewMoreClicked(){
                this.viewMore=!this.viewMore
                for (var i = this.thead.length - 1; i >= 0; i--) {
                    if(!this.thead[i].static){
                        this.thead[i].default = !this.thead[i].default
                    }
                    
                }
            },
            onRowSelected(id){
                this.$emit('selected',id)
            },
            
           
        },

    }
</script>