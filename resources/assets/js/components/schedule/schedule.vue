<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-calendar-o" aria-hidden="true"></i> 課程預定進度</h4>
            </span>
            
            <div class="form-inline">
                <div class="form-group">
                    <button :disabled="loading" class="btn btn-warning btm-sm" @click.prevent="beginImport">
                     從舊課程複製
                    </button>

                    <button v-if="loading" class="btn btn-default">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                    </button>
                    <label v-else :disabled="loading" class="btn  btn-success btn-file" @click="initExcel">
                       <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                       Excel 匯入
                       <input :disabled="loading" type="file"  ref="fileinput"  name="schedules_file" style="display: none;"  
                       @change="onFileChange" >
                    </label>
                    <small class="text-danger" v-if="hasError" v-text="err_msg"></small>

                    <a href="/downloads?type=import&key=schedules" target="_blank" class="btn btn-default btn-primary">
                        <i class="fa fa-download" aria-hidden="true"></i>下載範例檔
                    </a> 
                </div>
                <div class="form-group">
                    <button :disabled="loading" v-if="can_edit" class="btn btn-primary btm-sm" @click.prevent="beginCreate">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                    </button>
                </div>
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
           <h3>從舊課程複製</h3>
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
            },
            hasError(){
                if(this.err_msg) return true
                    return false
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
                },

                loading:false,
                files: [],

                err_msg:'',
               
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
            },
            initExcel(){
                this.err_msg=''
                this.loading=false
            },
            onFileChange(e) {
              
                var files = e.target.files || e.dataTransfer.files
                if (!files.length)  return
                   
                this.files = e.target.files

                this.submitExcelImport()
            },
            submitExcelImport() {
                this.loading=true
               
                let form = new FormData()
                for (let i = 0; i < this.files.length; i++) {
                    form.append('schedules_file', this.files[i])
                    form.append('course_id', this.course_id)
                    
                }

                let store=ScheduleImport.excelImport(form)
                store.then(result => {
                        Helper.BusEmitOK()
                        this.loading=false
                        this.init()                        
                    })
                    .catch(error => {
                        
                        if(error.response.data.code==422){
                            this.err_msg=error.response.data.error
                        }
                       
                        Helper.BusEmitError(error,'存檔失敗')

                        this.loading=false
                    })
            },
            
           
        },

    }
</script>