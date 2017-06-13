<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-clock-o" aria-hidden="true"></i> 課程時間</h4>
            </span>
            <div>
                <button v-if="canEdit" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">

            <edit-classtime v-for="classTime in classTimes" :classTime="classTime"
             :canEdit="canEdit"  :course_id="course_id"  @btnDeleteClicked="btnDeleteClicked" 
             @saved="classTimeUpdated">
                 
            </edit-classtime>

            


            <edit-classtime v-if="creating" :canEdit="canEdit"  :course_id="course_id"
             @saved="classTimeCreated"   @endEdit="endCreate" >
             
                   
            </edit-classtime>  

            
            
   

        </div><!-- End panel-body-->
    </div>

    <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteClassTime"  @closed="closeConfirm" ok-text="確定"
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

</div>

</template>

<script>
    import EditClassTime from '../../components/classtime/edit-classtime.vue'
    export default {
        name: 'ClassTime',
        components: {
             'edit-classtime':EditClassTime,
             'modal': Modal,
        },
        props: ['course_id','canEdit'],
        beforeMount() {
           this.init()
        },
        data() {
            return {
                loaded:false,
                creating:false,
                classTimes:[],
                showConfirm:false,
                confirmMsg:'',
                deleteId:0,
            }
        },
        methods: {
            init() {
                this.loaded=false
                this.creating=false
                this.showConfirm=false
                this.confirmMsg=''
                this.deleteId=0
                this.classTimes=[]
                this.fetchData()         
            }, 
            fetchData() {
                let url = '/api/classtimes?course=' + this.course_id                
                axios.get(url)
                    .then(response => {
                        this.classTimes=response.data.classTimes
                       
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
               
               this.$emit('endEditClassTime')
            },
            cancelCreate(){
               this.creating=false
               
            },
            btnDeleteClicked(values){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要刪除 ' + values.name + ' 嗎？'
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
            deleteClassTime(){

                let url = '/api/classtimes/' + this.deleteId 
                let form=new Form()
                form.delete(url)
                .then(result => {
                    this.init()
                    Helper.BusEmitOK('刪除成功')

                    this.$emit('classTimeChanged') 
                    this.deleteId=0;
                    this.closeConfirm();
                })
                .catch(error => {
                                   
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm();
                       
                })
            },
            classTimeUpdated(classTime){
                 this.$emit('classTimeChanged')  
            },
            classTimeCreated(classTime){      
                  this.init()  
                  this.$emit('classTimeChanged')  
            },
            
           
        },

    }
</script>