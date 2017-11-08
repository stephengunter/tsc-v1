<template>
    <data-viewer v-if="ready"  :default_search="defaultSearch" :default_order="defaultOrder"
        :source="source" :search_params="search_params"  :thead="getThead()" :no_search="no_search"  
        :filter="filter"  :title="title" :create_text="createText" 
        :show_header="show_header" :show_title="show_title"  :no_page="no_page" :editting="editting"
        :data_list="teacherList" :version="current_version"  
        @refresh="init"  @beginCreate="beginCreate"
        @dataLoaded="onDataLoaded" @edit="onEdit" @cancel-edit="onCancelEdit"
        @submit-edit="onSubmitEdit" @checkall="checkAll"   @uncheckall="unCheckAll">
            
         
         
        <template scope="props">
            <row :teacher="props.item" :group="group" :index="props.index"
                :details_link="details_link" :can_remove="can_remove"
                :single_select="single_select" :multi_select="multi_select"
                :been_selected="beenSelected(props.item.user_id)"  
                 :can_select_group="canSelectGroup" 
                @details="onDetails" @clear-error="onClearRowError"
                @selected="onRowSelected" 
                @checked ="onRowChecked" @unchecked="onRowUnChecked"
                @remove-clicked="onRemoveClicked" >
                
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
        name: 'TeacherList',
        props: {
            source_url:{
                type: String,
                default: ''
            },
            search_params: {
                type: Object,
                default: null
            },
            hide_create: {
                type: Boolean,
                default: false
            },
            create_text: {
                type: String,
                default: ''
            },
            version:{
                type: Number,
                default: 0
            },
            can_remove:{
               type: Boolean,
               default: false
            },
            details_link:{
               type: Boolean,
               default: true
            },
            single_select:{
               type: Boolean,
               default: false
            },
            multi_select:{
               type: Boolean,
               default: false
            },
            selected_ids: {
                type: Array,
                default: null
            },
            show_header:{
                type: Boolean,
                default: true
            },
            show_title:{
                type: Boolean,
                default: true
            },
            title_text:{
                type: String,
                default: ''
            },
            no_page:{
                type: Boolean,
                default: false
            },
            no_search:{
                type: Boolean,
                default: false
            },
            
        },
        beforeMount() {
            this.init()
        },
        watch: {
            version(val){
                this.current_version=val
            },
        },
        data() {
            return {
                ready:false,
                createText:'',

                group:false,

                allThead:Teacher.getThead(),
                selectColumn : {
                    title: '',
                    key: '',
                    sort: false,
                    static: true,
                    default: true,
                    checkall:this.multi_select,
                    checked:false
                },
                viewing:0,

                title:Helper.getIcon('Teachers') ,
                current_version:0,
                
                source: Teacher.source(),
                
                defaultSearch:'user.profile.fullname',
                defaultOrder:'updated_at',                       
                create: Teacher.createUrl(),
               

                filter: [{
                    title: '姓名',
                    key: 'user.profile.fullname',
                },{
                    title: '專長',
                    key: 'specialty',
                     
                }],

                
  
                teacherList:[],
                canEditNumber:false,
                hasData:false,
                viewMore:false,

                editting:''

              
             
            }
        },
        computed: {
            canSelectGroup(){
                if(!this.search_params) return true
                if(!this.search_params.hasOwnProperty('parent')) return true
                return parseInt(this.search_params.parent) > -1
            }
        },
        methods: {
           
            init() {
                if(this.search_params.hasOwnProperty('group')){
                    this.group=Helper.isTrue(this.search_params.group)
                }

                if(this.hide_create) this.createText=''
                else{
                    if(this.create_text) this.createText = this.create_text
                    else this.createText='新增教師'
                } 

                if(this.title_text) this.title += '  ' + this.title_text
                else {
                    if(this.group)this.title += '  教師群組管理'
                    else this.title += '  教師管理'
                }

                if(this.source_url) this.source=this.source_url
                this.current_version=this.version

                this.ready=true
                
            },
            getThead(){
                let thead=[]
                let staticThead=this.allThead.filter(item=>{
                    return item.static
                })

                if(this.single_select || this.multi_select){
                   
                    staticThead.splice(0, 0, this.selectColumn);
                }

                let nameThead =staticThead.find(item=>{
                      return item.key=='name'
                })

                
                if(this.group){
                    nameThead.title='名稱'
                    
                    thead=staticThead
                }else{
                    nameThead.title='姓名'
                    let defaultTheads= this.allThead.filter(item=>{
                        return item.default
                    })

                    thead=staticThead.concat(defaultTheads)
                }

               

                thead.sort((a, b) =>  {
                    return a.order - b.order
                })

                if(this.can_remove){
                    thead.push({
                        title: '',
                        key: '',
                        static: true,
                        sort: false
                    })
                }  

                return thead
            },
            cancelUnCheckAll(){
                this.selectColumn.checked=false
            },
            refresh(){
                this.current_version += 1
            },
            onDataLoaded(data){
                this.cancelUnCheckAll()
                
                
                this.hasData = parseInt(data.model.total) > 0
                let teacherList=data.model.data
                
                this.teacherList=teacherList
                this.$emit('data-loaded' , data)
            },

            checkAll(){
                this.selectColumn.checked=true
                this.$emit('checkall')
            },   
            unCheckAll(){
                this.selectColumn.checked=false
                this.$emit('uncheckall')
            },
            
            onDetails(id){
               
                this.$emit('details',id)
            },
            onRowSelected(teacher){
                this.$emit('selected',teacher)
            },
            onRowChecked(id){
                this.$emit('checked',id)
            },
            onRowUnChecked(id){
                this.$emit('unchecked',id)
            },
            beginCreate(){
                 this.$emit('begin-create')
            },
            beenSelected(id){
               
                if(!this.selected_ids) return false

                return this.selected_ids.includes(id)
            },
           
            onEdit(key){
                this.editting=key
            },
            onCancelEdit(key){
                this.refresh()
                this.editting=''
               
            },
            onSubmitEdit(key){
                if(key=='number') this.submitUpdateNumbers()
            },
            onClearRowError(index,name){
                //if(name=='number')
                //this.teacherList[index].numberError=''
            },
            onRemoveClicked(values){
               
                this.$emit('remove-clicked',values)
               
            }
            
            
           
        },

    }
</script>