<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-bell-o" aria-hidden="true"></i> 教室管理</h4>
            </span>
            <div>
                <select v-model="center"  @change="doSearch" class="form-control" >
                    <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 95%;">
             <thead> 
                <tr> 
                    <th style="width:15%"></th> 
                    <th style="width:20%">名稱</th> 
                    <th style="width:15%">狀態</th> 
                    <th style="width:20%">備註</th>
                    <th style="width:20%">最後更新</th> 
                    <th style="width:10%"></th>
                </tr> 
            </thead>
            <tbody> 
                <edit-classroom v-for="classroom in classroomList"  :classroom="classroom" 
                     @saved="classroomUpdated"  @btnDeleteClicked="btnDeleteClicked" >
                </edit-classroom>
                <edit-classroom v-if="creating" :center_id="center" @saved="classroomCreated"  @endEdit="endCreate" > </edit-classroom> 
            
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteclassroom"  @closed="closeConfirm" ok-text="確定"
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
    import EditClassroom from '../../components/classroom/edit-classroom.vue'
    export default {
        name: 'classroom',
        components: {
             'edit-classroom':EditClassroom,
             'modal': Modal,
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.classroomList.length) return true
                return false    
            }
        },
        data() {
            return {
                loaded:false,
                creating:false,
                classroomList:[],
                showConfirm:false,
                confirmMsg:'',
                deleteId:0,
                centerOptions:{},

                center:0
             
            }
        },
        methods: {
            init() {
                this.loaded=false

                this.creating=false
                this.showConfirm=false
                this.classroomList=[]
                this.confirmMsg=''
                this.deleteId=''
                this.centerOptions={}

                this.loadCenters()
                   .then(response => {
                        this.centerOptions=response.options
                        if(!this.center){
                            this.center=this.centerOptions[0].value
                        } 
                        this.fetchData()        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            }, 
            loadCenters(){
                let url = '/api/centers/options'  
                return new Promise((resolve, reject) => {
                    axios.get(url)
                    .then(response => {
                        
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject();
                    });
                });
                
               
            },
            fetchData() {
                let url = '/api/classrooms'  
                let query = '?center=' + this.center   
                url += query          
                axios.get(url)
                    .then(response => {
                       this.classroomList=response.data.classroomList
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            doSearch(){
               this.fetchData()
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
               
               this.$emit('endEditclassroom')
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
            deleteclassroom(){
                let url = '/api/classrooms/' + this.deleteId 
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
            classroomCreated(classroom){    
                   this.init()
            },
            classroomUpdated(classroom){ 
                  this.init()
            },
            
           
        },

    }
</script>