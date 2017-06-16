<template>
    <data-viewer v-if="loaded"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
         
        <div  class="form-inline" slot="header">
               
                <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="onBtnViewMoreClicked">
                    <span v-if="viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                    <span v-if="!viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
                </button>
        </div>
        <span slot="btn">
            <button class="btn btn-warning btn-sm" >
                <span class="glyphicon glyphicon-forward" aria-hidden="true"></span> 初始化
            </button>
        </span>
         <template scope="props">
            <row :lesson="props.item" :more="viewMore"
               @selected="onRowSelected">
                
            </row>
        </template>

    </data-viewer>

</template>

<script>
    import Row from './row.vue'
    export default {
        name: 'LessonList',
        components: {
            Row
        },
        props: {
            course_id: {
              type: Number,
              default: 0
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
                title:'<i class="fa fa-calendar-check-o" aria-hidden="true"></i>  課堂紀錄表',
                loaded:false,
                source: Lesson.source(),
                
                defaultSearch:'date',
                defaultOrder:'order',                
                create: Lesson.createUrl(),
                
                thead:[],
                filter: [{
                    title: '日期',
                    key: 'date',
                }],

                summary:null,

                statusOptions:[],
                searchParams:{
                    course : this.course_id,
                },
                hasData:false,
                viewMore:false
             
            }
        },
        computed: {
            createText(){
                if(this.hide_create) return ''
                return '新增報名'
            },
        },
        methods: {
            init() {
                this.loaded=false
               
                this.thead=Lesson.getThead()
                this.searchParams={
                            course : this.course_id,
                           
                        }
                this.loaded=true

                // if(this.course_id){
                //     let options = this.loadStatusOptions()
                //     options.then((value) => {
                //         this.searchParams={
                //             course : this.course_id,
                //             status : value
                //         }

                //        this.loaded=true
                //     })
                // }else{
                //     this.searchParams={
                //             user : this.user_id,
                //         }
                //     this.loaded=true
                // }
                
            },
            loadStatusOptions(){
                 return new Promise((resolve, reject) => {
                    let options=Lesson.statusOptions()
                    options.then(data => {
                        this.statusOptions = data.options
                        resolve(this.statusOptions[0].value);
                    })
                    .catch(error => {
                        console.log(error)
                        reject(error.response);
                    })
                })   //End Promise
            },
            onDataLoaded(data){
                // if(data.summary)  this.summary=data.summary
                // else this.summary=null
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