<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="form-inline">
                
                <div class="form-group">
                    <select  v-model="params.term"  @change="onParamChanged"  style="width:auto;" class="form-control selectWidth">
                        <option v-for="(item,index) in term_options" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select  v-model="params.center" @change="onParamChanged" style="width:auto;" class="form-control selectWidth">
                         <option v-for="(item,index) in center_options" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                
                

            </div>
            <div>
                <button v-if="!importing" v-show="hasData" @click.prevent="exportReport" class="btn btn-warning btn-sm">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> 匯出報表
                </button>
                <button v-if="importing" class="btn btn-default btn-sm">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                </button> 
                <label v-else  class="btn btn-sm btn-danger btn-file" @click="clearfile">
                      <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                       匯入停開課程
                       <input type="file"  ref="fileinput"  name="courses_file" style="display: none;"  
                       @change="onFileChange" >
                </label>
                <small class="text-danger" v-if="hasError" v-text="err_msg"></small>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
             <div class="panel-title">
                 <h4 v-html="title">
                 </h4>
             </div>
           
             
        </div>
        <div class="panel-body">
           
            <table class="table table-striped">
                <thead>
                    <tr>
                       
                        <th> 課程名稱</th>  
                        <th> 最低要求</th>
                        <th> 上限人數</th>
                        <th> 已繳費人數</th>
                    </tr>
                    
                </thead>
                <tbody>
                   <tr v-for="(course,index) in courses" :key="index" :course="course" >
                     
                      <td>  {{ course.fullName }}  </td>
                      <td>  {{ course.min }}  </td>
                      <td>  {{ course.limit }}  </td>
                      <td style="font-size:1.2em"> 
                        
                         <span v-text="course.summary.success" 
                               :class="getStyle(course)">
                         </span>  
                     
                      </td>  
                      
                   </tr>
                                   
                </tbody>
            </table>
        </div>
       
    </div>
    
    <form id="form-export" action="/signups-report" method="post">
        <input name="term" type="hidden" :value="params.term"  >
        <input name="center" type="hidden" :value="params.center"  >
    </form>
</div>

</template>

<script>
    

    export default {
        name: 'SignupsReport',     
        props:{
           term_options:{
              type:Array,
              default:null
           },
           center_options:{
              type:Array,
              default:null
           }
        },  
        components: {
             
        },
        data() {
            return {
               
                title:Helper.getIcon('signups') + ' 報名統計表',
                
               importing:false,
                
                params:{
                    term:0,
                    center:0,
                 
                },

                courses:[], 
               err_msg:''
             
            }
        },
        computed: {
            hasData () {
                return this.courses.length > 0
            },
            hasError(){
                if(this.err_msg) return true
                    return false
            },
        },
        mounted() {
             this.init()
        },
        methods: {
            init(){
              
               this.importing=false

               this.params.term=this.term_options[0].value
               this.params.center=this.center_options[0].value

               this.fetchData()
               
             
            },
            clearfile(){
               this.$refs.fileinput.value = null
               this.err_msg=''
            },
            fetchData(){
              
                let url=Helper.buildQuery('/signups-report',this.params)
                axios.get(url)
                .then(response => {
                   this.courses=response.data.courses
                })
                .catch(error=> {
                   Helper.BusEmitError(error)
                })
            },
           
            getStyle(course){
               if(Helper.isTrue(course.underMin)) return 'under-min'
               return ''
            },
            onParamChanged(){
                this.fetchData()
            },
            exportReport(){
                document.forms['form-export'].submit()
            },
            onFileChange(e) {
              
                var files = e.target.files || e.dataTransfer.files
                if (!files.length)  return
                   
                this.files = e.target.files

                this.submitImport()
            },
            submitImport(){
               this.importing=true
               
                let form = new FormData()
                for (let i = 0; i < this.files.length; i++) {
                    form.append('courses_file', this.files[i])
                    
                }

                let url = '/signups-report/import-stops'
                let method = 'post'
                axios.post(url, form)
                     .then(response => {
                        Helper.BusEmitOK()
                        this.importing=false
                       
                     })
                     .catch(error => {
                        if(error.response.data.code==422){
                           this.err_msg=error.response.data.error
                        }
                     
                        Helper.BusEmitError(error,'存檔失敗')

                        this.importing=false
                     })

                
            }
            
            
        },

    }
</script>

<style scoped>
   .under-min {
      color:red
   }
</style>
