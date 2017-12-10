<template>

    <div class="panel panel-default">
        <div class="panel-heading ">           
       

            <h4 v-html="title"></h4>
           
            
        </div> <!--  panel  heading -->
        <div  class="panel-body">
         
            <div class="row">
                
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
        name: 'VolunteerImport',
        
        data() {
            return {
                title:Helper.getIcon('Volunteers')  + '  Excel 匯入志工',
                
                loading:false,

                files: [],

                err_msg:'',

               

               
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
               
                return  url+'volunteers'
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

                

                let form = new FormData()
                for (let i = 0; i < this.files.length; i++) {
                    form.append('volunteers_file', this.files[i])
                   
                }

                let store=this.store(form)
                store.then(result => {
                        Helper.BusEmitOK()
                        this.loading=false
                        this.$emit('imported')                         
                    })
                    .catch(error => {
                        
                        if(error.response.data.code==422){
                            this.err_msg=error.response.data.error
                        }
                       
                        Helper.BusEmitError(error,'存檔失敗')

                        this.loading=false
                    })
            },
            store(form) {
                let url = '/volunteers-import'
                let method = 'post'
                return new Promise((resolve, reject) => {
                    axios.post(url, form)
                        .then(response => {
                            resolve(response.data);
                        })
                        .catch(error => {
                            reject(error);
                        })

                })
            }
           
            
            
        },

    }
</script>