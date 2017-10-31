<template>
    <data-viewer  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="search_params"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" create_text="新增課程" 
      :show_title="show_title"  :no_page="no_page" :editting="editting"
      @refresh="init" :version="current_version"   @beginCreate="beginCreate"
      @dataLoaded="onDataLoaded" @edit="onEdit" @cancel-edit="onCancelEdit"
      @submit-edit="onSubmitEdit">
        <div  class="form-inline" slot="header">
               
            <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="onBtnViewMoreClicked">
                <span v-if="viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                <span v-if="!viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
            </button>
        </div>
         
         
        <template scope="props">
            <row :course="props.item" :more="viewMore" :select="can_select"
               :been_selected="beenSelected(props.item.id)"    
               :editting_number="edittingNumber"   
               @selected="onRowSelected" @group-selected="onGroupSelected"
               @unselected ="onRowUnselected">
                
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
            can_select:{
               type: Boolean,
               default: true
            },
            selected_ids: {
              type: Array,
              default: null
            },
            show_title:{
               type: Boolean,
               default: true
            },
            no_page:{
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
            course_id(value) {
               this.searchParams.course=value
            },
            canEditNumber(value){
                
                let numberThead =this.thead.find(item=>{
                      return item.key=='number'
                })

                numberThead.edit=value
            }
        },
        data() {
            return {
                title:Helper.getIcon('Courses')  + '  課程管理',
                current_version:0,
                loaded:false,
                source: Course.source(),
                
                defaultSearch:'name',
                defaultOrder:'begin_date',                      
                create: Course.createUrl(),
                
                thead:Course.getThead(this.can_select)  ,
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
        },
        methods: {
            init() {
                this.current_version=this.version
            },
            refresh(){
                this.current_version += 1
            },
            onDataLoaded(data){
                
                if(data.canEditNumber){
                    this.setCanEditNumber(data.canEditNumber)
                }else{
                    this.setCanEditNumber(false)
                } 
                
              
                this.courseList=data.model.data
                this.hasData=data.model.total
            },
            setCanEditNumber(val){
                if(this.can_edit_number){
                    this.canEditNumber=val
                }else{
                    this.canEditNumber=false
                }
            },
            onBtnViewMoreClicked(){
                this.viewMore=!this.viewMore
                for (var i = this.thead.length - 1; i >= 0; i--) {
                    if(!this.thead[i].static){
                        this.thead[i].default = !this.thead[i].default
                    }
                    
                }
            },
            onRowSelected(id,number,name){
                this.$emit('selected',id,number,name)
            },
            onRowUnselected(id){
                this.$emit('unselected',id)
            },
            beginCreate(){
                 this.$emit('begin-create')
            },
            beenSelected(id){
                if(!this.selected_ids) return false
                if(this.selected_ids.length < 1)  return false
                 let index = this.selected_ids.indexOf(id)
                return index >= 0
            },
            onGroupSelected(id){
                 this.$emit('group-selected',id)
            },
            onEdit(key){
                this.editting=key
            },
            onCancelEdit(key){
                this.editting=''
            },
            onSubmitEdit(key){
                if(key=='number') this.submitUpdateNumbers()
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
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            }  
            
           
        },

    }
</script>