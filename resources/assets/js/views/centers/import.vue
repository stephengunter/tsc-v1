<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading ">           
       

            <h4 v-html="title"></h4>
            
            <a href="/downloads?type=import&key=centers" target="_blank" class="btn btn-default btn-primary">
                <i class="fa fa-download" aria-hidden="true"></i>下載範例檔
            </a> 
            
            
        </div> <!--  panel  heading -->
        <div  class="panel-body">
         
            <div class="row">
                <div class="col-sm-4" >
                    <button v-if="loading" class="btn btn-default">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                    </button>
                    <label v-else  :disabled="loading" class="btn  btn-success btn-file" @click="init">
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

    <div v-show="areas">
        <h3>區域代碼對照表</h3>
        <ul style="font-size:1.5em">
            <li v-for="(area,index) in areas" :key="index">
                {{ area.text }}： {{ area.value }}
            </li>
        </ul>
       
    </div>

</div>   
</template>

<script>
    
    export default {
        name: 'CenterImport',
        props: {
            areas:{
               type: Array,
               default: null
            },
        },
        data() {
            return {
                title:Helper.getIcon('centers')  + '  Excel 匯入開課中心',
                
                loading:false,

                files: [],

                err_msg:''
               
            }
        },
        computed:{
            hasError(){
                if(this.err_msg) return true
                    return false
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
                    form.append('centers_file', this.files[i])
                    
                }

                let store=Center.import(form)
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
           
            
            
        },

    }
</script>