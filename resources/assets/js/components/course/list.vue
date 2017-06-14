<template>
    <data-viewer  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="search_params"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" create_text="" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
         <div  class="form-inline" slot="header">
               
                <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="onBtnViewMoreClicked">
                    <span v-if="viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                    <span v-if="!viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
                </button>
         </div>
         
         <template scope="props">
            <row :course="props.item" :more="viewMore"
               @selected="onRowSelected">
                
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
                title:Helper.getIcon('Courses')  + '  報名紀錄',
                loaded:false,
                source: Course.source(),
                
                defaultSearch:'name',
                defaultOrder:'begin_date',                      
                create: Course.createUrl(),
                
                thead:[],
                filter: [{
                    title: '名稱',
                    key: 'name',
                },{
                    title: '開始日期',
                    key: 'begin_date',
                }],
  
                
                hasData:false,
                viewMore:false
             
            }
        },
        
        methods: {
            init() {
                this.thead=Course.getThead(this.can_select)  
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
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            
           
        },

    }
</script>