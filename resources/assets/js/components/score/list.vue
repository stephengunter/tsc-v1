<template>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>
            <div v-show="editting">
                <button @click="onSubmit" class="btn btn-sm btn-success">
                  <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                  存檔

                </button>
                    
                <button @click="onCancel" class="btn btn-sm btn-default">
                   <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                   取消
                </button>
              
            </div>
            <div v-show="!editting">
                <button v-show="hasData" @click.prevent="exportExcel" class="btn btn-warning btn-sm">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> 匯出Excel
                </button>
                <label  class="btn btn-sm btn-success btn-file">
                  <span class="glyphicon  glyphicon-forward" aria-hidden="true"></span>
                    匯入
                  <input type="file" id="scores_file_input" name="scores_file" style="display: none;"  
                   @change="onFileChange" >
                </label>
                <button  v-show="can_edit" @click="editting=true" class="btn btn-primary btn-sm" >
                        <span class="glyphicon glyphicon-pencil"></span> 編輯
                </button>
            </div>
            
            
        </div>  <!-- End panel-heading-->
        
          
        <div  class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 90%;">
                <thead> 
                    <tr> 
                       
                        <th>學員編號</th> 
                        <th>學員姓名</th> 
                        <th style="width:20%">成績</th> 
                        <th style="width:25%">備註</th>
                    </tr> 
                </thead>
                <tbody> 
                    <tr v-for="(student, index) in studentList">
                        <td v-text="student.number"></td>
                        <td v-text="student.name"></td>
                        <td v-if="editting">
                            <input @keydown="clearErrorMsg(index)" type="text" name="student.score.points" class="form-control" v-model="student.score.points">
                  
                            <small class="text-danger" v-text="student.error"></small>
                        </td>
                        <td v-else v-text="student.score.points"></td>

                        <td v-if="editting">
                            <input  type="text" name="student.score.ps" class="form-control" v-model="student.score.ps">                 
                           
                        </td>
                        <td v-else v-text="student.score.ps"></td>
                    </tr>
                       

                       
                        
                </tbody>
                
            </table>
           
        </div><!-- End panel-body-->
        
        <form id="form-export" action="/scores/export" method="post">
            <input name="course" type="hidden" :value="course_id"  >
            
        </form>
    </div>   
</template>

<script>
    import Row from './row.vue'
    export default {
        name: 'ScoreList',
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
       
        mounted(){
            this.init()
        },
        data() {
            return {
                title:Helper.getIcon(Score.title())  + '  學員成績',
                loaded:false,
                editting:false,
              
                studentList:[],
                rowSettings:{
                    creating:false,
                    can_select:false,
                    show_updated:false,
                    can_edit:true
                },

                files: [],

                hasError:false,
             
            }
        },
        computed: {
            hasData() {
              return this.studentList.length > 0
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

                this.loaded=false
                this.editting=false
                this.studentList=[]
                document.getElementById("scores_file_input").value = ''
               
                this.fetchData()
                
            },
            fetchData(){
                let getData=Score.index(this.course_id)
                    getData.then(data => {
                        let studentList=data.studentList
                        for(let i=0; i<studentList.length ; i++){
                           let student=studentList[i]
                           student.score.points=Helper.formatMoney(student.score.points)
                       
                        }
                       this.studentList=studentList
                      
                       this.loaded = true
                        
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                    })
            },
            beginImport(){
               alert('beginImport')
            },
            onFileChange(e) {
                var files = e.target.files || e.dataTransfer.files
                if (!files.length)  return
                   
                this.files = e.target.files

                this.submitImport()
            },
            submitImport() {
                this.submitting = true
                let form = new FormData();
                form.append('course', this.course_id);

                for (let i = 0; i < this.files.length; i++) {
                    form.append('score_file', this.files[i])
                }

                let store=Score.import(form)
                store.then(result => {
                        Helper.BusEmitOK()
                        this.init()
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            },
            onSubmit(){
               this.hasError=true
               let errors=0
               for(let i=0; i<this.studentList.length ; i++){
                    let student=this.studentList[i]
                    if(isNaN(student.score.points)){
                          student.error= '分數必須為數字'
                          errors+=1
                    }else{
                        student.error= ''
                    }
                   
                }
                if(errors==0) {
                    this.submitForm()
                }
            },
            onCancel(){
                this.init()
            },
            submitForm(){
                let form=new Form({
                    course:this.course_id,
                    studentList:this.studentList
                })
                let store=Score.store(form)
                store.then(result => {
                        Helper.BusEmitOK()
                        this.init()
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            },
            clearErrorMsg(index){
                let student=this.studentList[index]
                student.error= ''
            },
            exportExcel(){
                document.forms['form-export'].submit()
            } 
            
            
            
           
        },

    }
</script>

<style scoped>
  .btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
</style>