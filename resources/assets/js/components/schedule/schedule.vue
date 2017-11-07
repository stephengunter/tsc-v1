<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-calendar-o" aria-hidden="true"></i> 課程預定進度</h4>
            </span>
            
            <div>
               
                <button v-if="can_edit" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData || creating" class="table table-striped" style="width: 99%;">
                <thead> 
                    
                    <tr v-if="indexMode"> 
                        <th style="width:7%">#</th> 
                        <th style="width:25%">課目標題</th> 
                        <th style="width:25%">內容</th> 
                        <th style="width:15%">教材</th>
                        <th style="width:15%">最後更新</th>
                        <th style="width:8%"></th> 
                    </tr> 
                    <tr v-else>
                        <th style="width:12%">順序</th> 
                        <th style="width:30%">課目標題</th> 
                        <th style="width:30%">內容</th> 
                        <th style="width:20%">教材</th>
                        <th style="width:8%"></th>
                    </tr>
                   
                </thead>
                <tbody> 
                    <edit v-if="creating" :course_id="course_id"  @saved="onCreated" 
                       @canceled="onCreateCanceled" > 
                    </edit>  

                    <edit  v-for="(schedule,index) in scheduleList" :key="index"  :schedule="schedule" 
                        :can_edit="can_edit" :editting="!indexMode"
                        @editting="onEditting" @canceled="onEditCanceled"
                        @saved="onUpdated"  @btn-delete-clicked="beginDelete" >
                    </edit>
                </tbody>
            
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
    <modal :showbtn="false" :width="importSettings.width" :show.sync="importSettings.show"  @closed="importCanceled" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="importCanceled">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           <h3>從舊課程中匯入</h3>
          </div>
        <div slot="modal-body" class="modal-body">
           
            <importor v-if="importSettings.show" :course_id="course_id" 
              @success="onImportSuccess" @failed="onImportFailed">
                
            </importor>
      
        </div>
    </modal>       

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeleteCanceled" @confirmed="deleteSchedule">        
    </delete-confirm>

</div>

</template>

<script>
    import Edit from '../../components/schedule/edit.vue'
    import Importor from '../../components/schedule/importor.vue'
    export default {
        name: 'Schedule',
        components: {
             Edit,
             Importor
        },
        props: {
            course_id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.scheduleList.length) return true
                return false    
            },
            indexMode(){
                if(this.creating) return false
                if(this.seleted) return false
                    return true
            }
        },
        data() {
            return {
                loaded:false,
                creating:false,
                seleted:0,
                scheduleList:[],

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                },

                importSettings:{
                    course_id:0,
                    show:false,
                    width:1000,
                }
               
            }
        },
        methods: {
            init() {
                this.loaded=false

                this.creating=false
                this.seleted=0
                
                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }

                this.importSettings={
                    course_id:0,
                    show:false,
                    width:1000,
                }

                this.scheduleList=[]
                this.fetchData()
            },
            fetchData() {
                let getData=Schedule.index(this.course_id)
                    getData.then(data => {
                      
                       this.scheduleList=data.scheduleList
                       this.loaded = true
                        
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                    })
            },
            
            beginCreate(){
                this.creating=true
            },
            endCreate(){
                 this.creating=false
            },
            onEditting(id){
                this.seleted=id
            },
            OnEditCanceled(){
                this.seleted=0
            },
            cancelCreate(){
               this.creating=false
               
            },
            
            beginDelete(values){
                this.deleteConfirm.msg='確定要刪除 ' + values.name + ' 嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                
            },
            onDeleteCanceled(){
                this.deleteConfirm.show=false
            },
            deleteSchedule(){
                 let id = this.deleteConfirm.id 
                let remove= Schedule.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.init()
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },

            onCreated(schedule){    
                this.init()
                this.$emit('created',schedule)
            },
            onCreateCanceled(){
                this.init()
            },
            onEditCanceled(){
                this.init()
            },
            onUpdated(schedule){ 
                 this.init()
                 this.$emit('updated',schedule)
            },
            beginImport(){
                this.importSettings.course_id=this.course_id
                this.importSettings.show=true
            },
            importCanceled(){
                this.importSettings.show=false
            },
            onImportSuccess(){
                this.importSettings.show=false
                Helper.BusEmitOK('匯入成功')
                this.init()
            },
            onImportFailed(error){
                this.importSettings.show=false
                Helper.BusEmitError(error,'匯入失敗')
            }
            
           
        },

    }
</script>