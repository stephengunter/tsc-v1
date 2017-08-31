<template>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>
            <label  slot="btn"  class="btn btn-sm btn-warning   btn-file">
              匯入
              <input type="file" name="scores_file" style="display: none;"  
               @change="onFileChange" >
            </label>
       
            
            
        </div>  <!-- End panel-heading-->
        <form class="form" @submit.prevent="onSubmit" >
          
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
                        <td v-if="can_edit">
                            <input @keydown="clearErrorMsg(index)" type="text" name="student.score.points" class="form-control" v-model="student.score.points">
                  
                            <small class="text-danger" v-text="student.error"></small>
                        </td>
                        <td v-else v-text="student.score.points"></td>
                        <td v-text="student.score.ps"></td>
                    </tr>
                       

                       
                        
                </tbody>
                
            </table>
           
        </div><!-- End panel-body-->
        <div class="panel-footer">
            <button type="submit" class="btn  btn-sm btn-success pull-right">存檔</button>
            <div class="clearfix"></div>
        </div>
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
        beforeMount() {
           this.init()
        },
        data() {
            return {
                title:Helper.getIcon(Score.title())  + '  學員成績',
                loaded:false,
              
                studentList:[],
                rowSettings:{
                    creating:false,
                    can_select:false,
                    show_updated:false,
                    can_edit:true
                },

                files: [],
             
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
                this.studentList=[]
                this.fetchData()
                
            },
            fetchData(){
                let getData=Score.index(this.course_id)
                    getData.then(data => {
                      
                       this.studentList=data.studentList
                      
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
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.files = e.target.files;

                this.submitImport();
            },
            submitImport() {
                this.submitting = true
                let form = new FormData();
               

                for (let i = 0; i < this.files.length; i++) {
                    form.append('score_file', this.files[i]);
                }

                let store=Score.import(form)
                store.then(result => {
                        // this.$emit('uploaded', photo)
                        // this.removeImage()
                        // this.submitting = false
                    })
                    .catch(error => {
                        // this.removeImage()
                        // Helper.BusEmitError(error,'上傳失敗')
                        // this.submitting = false
                    })
            },
            onSubmit(){
               
               for(let i=0; i<this.studentList.length ; i++){
                    let student=this.studentList[i]
                    if(isNaN(student.score.points)){
                          student.error= '分數必須為數字'
                    }else{
                        student.error= ''
                    }
                   
                }
            },
            clearErrorMsg(index){
                
            },
            onDataLoaded(data){
                // this.course=data.course
                // this.$emit('loaded',data)

            }, 
            
            
            
           
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