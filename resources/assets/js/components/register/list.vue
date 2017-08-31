<template>
<div>
    <data-viewer  :default_search="defaultSearch" :default_order="defaultOrder" :default_direction="defaultDirection"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :no_page="no_page" :show_title="show_title"  :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="current_version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
     
         
         <button  slot="btn"   v-show="can_edit" @click="btnAddClicked" class="btn btn-primary btn-sm" >
              <span class="glyphicon glyphicon-plus"></span> 新增學員
         </button>
         
         <template scope="props">
            <row  :student="props.item"
                :creating="rowSettings.creating" 
                :can_select="rowSettings.can_select" 
                :show_updated="rowSettings.show_updated"
                :can_edit="rowSettings.can_edit"  
                @selected="onSelected"
                @student-selected="onStudentSelected"
                @remove="onRemove"  >
                
               
            </row>
         </template>

    </data-viewer>

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="deleteConfirm.show=false" @confirmed="submitDelete">        
    </delete-confirm>

</div>
</template>

<script>
    import Row from './row.vue'
    export default {
        name: 'StudentList',
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
                title:Helper.getIcon(Register.title())  + '  註冊學員名單',
                loaded:false,
                
                
                current_version:0,
                             
                createText: '',
                thead:[],
                filter: [],
                defaultSearch:'id',
                defaultOrder:'number',
                defaultDirection:'asc',

                
                statusOptions:[],
                searchParams:{},
             
                hasData:false,
                viewMore:false,

                course:null,

                rowSettings:{
                    creating:false,
                    can_select:false,
                    show_updated:true,
                    can_edit:true
                },

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }  
             
            }
        },
        computed: {
            source() {
               return  Register.showUrl(this.course_id)
            },
            
        },
        watch: {
          version() {
             this.current_version+=1
          },
          course_id() {
             this.current_version+=1
          }
        },
        methods: {
            init() {
                
                this.thead=Register.getThead(this.rowSettings.show_updated)
                   let thRemove={
                      title: '',
                      key: 'remove',
                      sort: false,
                      default:true
                   }
                this.thead.splice(0, 0, thRemove)
                
                
            },
           
            onDataLoaded(data){
                this.course=data.course
                this.$emit('loaded',data)

            }, 
            
            onSelected(user_id){
                this.$emit('selected',user_id)                
            },
            onStudentSelected(id){
               this.$emit('student-selected',id)
            },
            beginCreate(){
                 this.$emit('begin-create')
            },
            btnAddClicked(){
               this.$emit('edit')   
            },
            onRemove(values){
                this.deleteConfirm.msg= '確定要將 ' + values.name + ' 從註冊學員名單中刪除嗎' 
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true 
                
            },
            submitDelete(){
                let id = this.deleteConfirm.id 
                let remove= Student.delete(id)
                remove.then(result => {
                    this.current_version+=1
                    Helper.BusEmitOK('刪除成功')
                    this.deleteConfirm.show=false
                    this.$emit('student-deleted')
                    
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.deleteConfirm.show=false   
                })
               
            }
            
           
        },

    }
</script>