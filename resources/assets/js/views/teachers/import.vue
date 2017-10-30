<template>

    <div class="panel panel-default">
        <div class="panel-heading ">           
       

            <h4 v-html="title"></h4>
            
            <a  :href="downloadUrl" target="_blank" class="btn btn-default btn-primary">
                <i class="fa fa-download" aria-hidden="true"></i>下載範例檔
            </a> 
           
            
        </div> <!--  panel  heading -->
        <div  class="panel-body">
         
            <div class="row">
                <div class="col-sm-4">
                      <toggle :items="typeOptions"   :default_val="1" @selected="onTypeSelected"></toggle>
                </div>
                <div class="col-sm-4" >
                    <button v-if="loading" class="btn btn-default">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                    </button>
                    <label v-else :disabled="loading" class="btn  btn-success btn-file" @click="init">
                       <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                       Excel 匯入
                       <input :disabled="loading" type="file"  ref="fileinput"  name="admins_file" style="display: none;"  
                       @change="onFileChange" >
                    </label>
                    <small class="text-danger" v-if="hasError" v-text="err_msg"></small>
                </div>
            </div>
            

        </div> <!--  panel  body -->
       
    </div>  <!--  panel  -->

   
</template>

<script>
    
    export default {
        name: 'TeacherImport',
        
        data() {
            return {
                title:Helper.getIcon('Teachers')  + '  Excel 匯入教師',
                
                loading:false,

                type:true,

                files: [],

                err_msg:'',

                typeOptions:[{
                    text: '個人教師',
                    value: '1'
                }, {
                    text: '教師群組',
                    value: '0'
                }]


               
            }
        },
        computed:{
            isGroup(){
               return !Helper.isTrue(this.type)
            },
            hasError(){
                if(this.err_msg) return true
                    return false
            },
            downloadUrl(){
                let url='/downloads?type=import&key='
                if(this.isGroup) return url+'teacher-groups'
                return  url+'teachers'
            }
        
        },
        beforeMount() {
             
        },
        methods: {
            init(){
               this.$refs.fileinput.value = null
               this.err_msg=''
               this.loading=false
            },
            onTypeSelected(val){
               this.type=val              
            },
            onFileChange(e) {
              
                var files = e.target.files || e.dataTransfer.files
                if (!files.length)  return
                   
                this.files = e.target.files

                this.submitImport()
            },
            submitImport() {
                this.loading=true

                let type=1
                if(!Helper.isTrue(this.type)) type=0

                let form = new FormData()
                for (let i = 0; i < this.files.length; i++) {
                    form.append('teachers_file', this.files[i])
                    form.append('type', type)
                }

                let store=TeacherImport.store(form)
                store.then(result => {
                        Helper.BusEmitOK()
                        this.loading=false
                        this.$emit('imported',this.isGroup)                         
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