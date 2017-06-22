<template>
    <data-viewer v-if="loaded"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :no_page="no_page" :show_title="show_title"  :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
     
         <div v-if="course_id"  class="form-inline" slot="header">
             <span v-if="summary" >
              總數：{{ summary.total }} 筆 &nbsp; 已繳費：{{ summary.success }} 筆 &nbsp; 待繳費：{{ summary.default }} 筆 &nbsp; 已取消：{{ summary.canceled }} 筆
              &nbsp;&nbsp;&nbsp;&nbsp;
              </span>
              <select v-model="searchParams.status"  style="width:auto;" class="form-control selectWidth">
                  <option v-for="item in statusOptions" :value="item.value" v-text="item.text"></option>
              </select>
               
         </div>
         <button  slot="btn"  v-if="canDelete" v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
              <span class="glyphicon glyphicon-trash"></span> 刪除
         </button>
         
         <template scope="props">
            <row  :admit="props.item"
                :can_select="rowSettings.can_select" 
                :show_updated="rowSettings.show_updated"
                :can_edit="rowSettings.can_edit"  
                @selected="onSelected"
                @remove="onRemove"
                >
               
            </row>
         </template>

    </data-viewer>

</template>

<script>
    import Row from './row.vue'
    export default {
        name: 'AdmitList',
        components: {
            Row,
        },
        props: {
            course_id: {
              type: Number,
              default: 0
            },
            creating:{
              type: Boolean,
              default: false
            },
            hide_create: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            can_select:{
               type: Boolean,
               default: false
            },
            no_page:{
               type: Boolean,
               default: false
            },
            show_title:{
               type: Boolean,
               default: true
            },
        },
        beforeMount() {
           this.init()
        },
        data() {
            return {
                title:Helper.getIcon(Admission.title())  + '  錄取名單',
                loaded:false,
                source: Admission.showUrl(this.course_id),
                
                             
                createText: '',
                thead:[],
                filter: [],
                defaultSearch:'id',
                defaultOrder:'id',

                summary:null,

                statusOptions:[],
                searchParams:{   },
             
                hasData:false,
                viewMore:false,

                course:null,

                rowSettings:{
                    can_select:false,
                    show_updated:true,
                    can_edit:true
                },
             
            }
        },
        computed: {
            canDelete: function () {
                if(!this.course) return false
                if(!this.course.admission) return false
                return this.course.admission.canDelete
            }
        },
        methods: {
            init() {
                
                let options = this.loadStatusOptions()
                options.then((value) => {
                    this.searchParams={
                        status : value
                    }

                   this.loaded=true
                })
               
                this.thead=Admission.getThead(this.rowSettings.show_updated)
                let thRemove={
                    title: '',
                    key: 'remove',
                    sort: false,
                    default:true
                 }
                this.thead.splice(0, 0, thRemove)

            },
            loadStatusOptions(){
                 return new Promise((resolve, reject) => {
                    let options=Signup.statusOptions()
                    options.then(data => {
                        this.statusOptions = data.options
                        let allStatuses={ text:'總數' , value:'-9' }
                        this.statusOptions.splice(0, 0, allStatuses);
                        resolve(this.statusOptions[0].value);
                    })
                    .catch(error => {
                        console.log(error)
                        reject(error.response);
                    })
                })   //End Promise
            },
            onDataLoaded(data){
                this.course=data.course
                this.$emit('loaded',data)

                if(data.summary) {
                    this.summary=data.summary
                }else{
                    this.summary=null
                } 



            }, 
            statusStyle(status){
                return 'btn-xs btn btn-' + Signup.getStatusStyle(status)
            },
            statusText(status){
                return Signup.getStatusText(status)
            },
            discountText(signup){
                if(!signup.discount) return ''
                return Signup.formatDiscountText(signup.discount, signup.points)
            },
            onSelected(signup_id){this.$emit('selected',signup_id)
                
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            onRemove(id){
              alert(id)
            },
            btnDeleteClicked(){
                 let values={
                    // name: this.category.name,
                    // id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
           
        },

    }
</script>