<template>
    <data-viewer v-if="ready"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="search_params"  :thead="getThead()" :no_search="no_search"  
      :filter="filter"  :title="title" :create_text="createText" 
      :show_header="show_header" :show_title="show_title"  :no_page="no_page" :editting="editting"
      :data_list="courseList" :version="current_version"  
      @refresh="init"  @beginCreate="beginCreate"
      @dataLoaded="onDataLoaded" @edit="onEdit" @cancel-edit="onCancelEdit"
      @submit-edit="onSubmitEdit" @checkall="checkAll"   @uncheckall="unCheckAll">
        <div  class="form-inline" slot="header">
               
            <button v-show="viewing > 0" class="btn btn-default btn-xs" @click.prevent="viewing--">
                <span class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>                
            </button>
            <button v-show="viewing <=2" class="btn btn-default btn-xs" @click.prevent="viewing++">
                <span class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
            </button>

        </div>
         
         
        <template scope="props">
            <row :course="props.item" :viewing="viewing" :index="props.index"
                :details_link="details_link" :can_remove="can_remove"
                :single_select="single_select" :multi_select="multi_select"
                :been_selected="beenSelected(props.item.id)"  
                :editting_number="edittingNumber"  :can_select_group="canSelectGroup" 
                @details="onDetails" @clear-error="onClearRowError"
                @selected="onRowSelected" @group-selected="onGroupSelected"
                @checked ="onRowChecked" @unchecked="onRowUnChecked">
                
            </row>
            
        </template>

    </data-viewer>
</template>

<script>
    import Row from '../../components/course/row.vue'
   
    export default {
        components: {
            Row
        },
        name: 'CourseList',
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
            can_edit_number:{
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

                allThead:Course.getThead(),
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

                title:Helper.getIcon('Courses') ,
                current_version:0,
                
                source: Course.source(),
                
                defaultSearch:'name',
                defaultOrder:'begin_date',                      
                create: Course.createUrl(),
               

                filter: [{
                    title: '名稱',
                    key: 'name',
                },{
                    title: '編號',
                    key: 'number',
                },{
                    title: '開始日期',
                    key: 'begin_date',
                }],
  
                courseList:[],
                canEditNumber:false,
                hasData:false,
                viewMore:false,

                editting:''

              
             
            }
        },
        computed: {
            edittingNumber(){
                return this.editting=='number'
            },
            canSelectGroup(){
                if(!this.search_params) return true
                if(!this.search_params.hasOwnProperty('parent')) return true
                return parseInt(this.search_params.parent) > -1
            }
        },
        methods: {
           
            init() {
                if(this.hide_create) this.createText=''
                else this.createText='新增課程'

                if(this.title_text) this.title += '  ' + this.title_text
                else this.title += '  課程管理'

                if(this.source_url) this.source=this.source_url
                this.current_version=this.version

                this.ready=true
                
            },
            getThead(){
                
                let staticThead=this.allThead.filter(item=>{
                    return item.static
                })

                if(this.single_select || this.multi_select){
                   
                    staticThead.splice(0, 0, this.selectColumn);
                }
               


                let thead= this.allThead.filter(item=>{
                    return item.view == this.viewing
                })

                let numberThead =staticThead.find(item=>{
                      return item.key=='number'
                })
                
                numberThead.edit=this.canEditNumber

                return staticThead.concat(thead)
            },
            setCanEditNumber(val){
                if(this.can_edit_number){
                    this.canEditNumber=val
                }else{
                    this.canEditNumber=false
                }
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
                let courseList=data.model.data

                if(data.canEditNumber && this.hasData){
                    this.setCanEditNumber(true)
                   
                }else{
                    this.setCanEditNumber(false)
                } 

                if(data.parentCourse){
                    courseList.splice(0,0,data.parentCourse)                   
                }
                
                this.courseList=courseList

               
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
            onRowSelected(course){
                this.$emit('selected',course)
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
            onGroupSelected(id){
                 this.$emit('group-selected',id)
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
                //name=='number'
                this.courseList[index].numberError=''
            },
            submitUpdateNumbers(){
                let form=new Form({
                    courses:this.courseList
                })
                let save=Course.updateNumbers(form)
                save.then(result => {
                        Helper.BusEmitOK()
                        
                        this.refresh()
                        this.editting=''
                    })
                    .catch(error => {
                        if(error.data.code==422){
                            let error_ids=error.data.error
                            error_ids.forEach(id=>{
                                let course=this.courseList.find(item=>{
                                    return item.id==id
                                })
                                course.numberError='編號重複'
                           })
                        }
                       
                        Helper.BusEmitError(error,'存檔失敗')
                        
                    })
            }  
            
           
        },

    }
</script>