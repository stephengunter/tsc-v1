<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-calendar-o" aria-hidden="true"></i> 課程預定進度</h4>
            </span>
            <div>
                
                <button v-if="canEdit" class="btn btn-warning btn-sm" @click.prevent="beginImport">
                    <span aria-hidden="true" class="glyphicon glyphicon-forward"></span> 匯入
                </button>
                <button   v-if="canEdit" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 95%;">
             <thead> 
                <tr> 
                    <th style="width:7%">#</th> 
                    <th style="width:35%">課目標題</th> 
                    <th style="width:35%">內容</th> 
                    <th style="width:15%">材料</th>
                    <th style="width:8%"></th> 
                </tr> 
            </thead>
            <tbody> 
                <edit-schedule v-for="schedule in scheduleList"  :schedule="schedule" 
                     @saved="scheduleUpdated"  @btnDeleteClicked="btnDeleteClicked" >
                </edit-schedule>
                
            </tbody> 
            </table>
            

            


            <create-schedule v-if="creating"  :course_id="course_id" @saved="scheduleCreated"
               @cancelCreate="endCreate" >
                   
            </create-schedule>  

        </div><!-- End panel-body-->

    </div>

    <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteschedule"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
          <div slot="modal-header" class="modal-header modal-header-danger">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="closeConfirm">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
             <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <h3 v-text="confirmMsg"> </h3>
        </div>
    </modal>

     <modal :showbtn="false" :width="1000" :show.sync="showImport"  @closed="showImport=false" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="showImport=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           <h3>從舊課程中匯入</h3>
          </div>
        <div slot="modal-body" class="modal-body">
           
            <teacher-course v-if="showImport" :course_id="course_id" @courseSelected="importSchedules"></teacher-course>
      
        </div>
    </modal>

</div>

</template>

<script>
    import EditSchedule from '../../components/schedule/edit-schedule.vue'
    import CreateSchedule from '../../components/schedule/create-schedule.vue'
    import TeacherCourse from '../../components/teacher-course/teacher-course.vue'
    export default {
        name: 'Schedule',
        components: {
             'edit-schedule':EditSchedule,
             'create-schedule':CreateSchedule,
             'teacher-course':TeacherCourse,
             'modal': Modal,
        },
        props: ['course_id', 'canEdit'],
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.scheduleList.length) return true
                return false    
            }
        },
        data() {
            return {
                loaded:false,
                creating:false,
                scheduleList:[],
                showConfirm:false,
                confirmMsg:'',
                showImport:false,
                deleteId:0,
                orderOptions:{},
             
            }
        },
        methods: {
            init() {
                this.loaded=false
                this.creating=false

                this.showConfirm=false
                this.confirmMsg=''

                this.showImport=false

                this.scheduleList=[]
              
                this.deleteId=''
                this.orderOptions={}
                
                this.fetchData()         
            }, 
            fetchData() {
                let url = '/api/schedules?course=' + this.course_id                
                axios.get(url)
                    .then(response => {
                       
                       this.scheduleList=response.data.scheduleList
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            beginCreate(){
                this.creating=true
            },
            endCreate(){
                 this.creating=false
            },
            cancelEdit(){
               
               this.$emit('endEditschedule')
            },
            cancelCreate(){
               this.creating=false
               
            },
            btnDeleteClicked(values){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要刪除課程進度 ' + values.name + ' 嗎？'
                    this.deleteId=values.id
                    this.showConfirm=true
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
                
            },
            closeConfirm(){
                this.showConfirm=false
            },
            deleteschedule(){
                let url = '/api/schedules/' + this.deleteId 
                let form=new Form()
                form.delete(url)
                .then(result => {
                    this.init()
                    Helper.BusEmitOK('刪除成功')
                    this.deleteId=0;
                    this.closeConfirm();
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    
                    this.closeConfirm();
                       
                })
            },
            scheduleCreated(schedule){    
                   this.init()
            },
            scheduleUpdated(schedule){ 
                  this.init()
            },
            beginImport(){
                 this.showImport=true
            },
            importSchedules(course){
                let form=new Form({
                    from_course:course,
                    to_course:this.course_id
                })
                let url='/api/schedules/import'
                form.post(url)
                    .then(result => {
                        this.init()
                        Helper.BusEmitOK('匯入成功')
                        
                        this.showImport=false
                    })
                    .catch(error => {
                                             
                        Helper.BusEmitError(error,'匯入失敗')
                       
                        this.showImport=false
                           
                    })
            }
            
           
        },

    }
</script>