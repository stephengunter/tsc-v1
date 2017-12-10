<template>

    <div v-if="center_options" class="panel panel-default">
        <div class="panel-heading ">           
       

            <h4 v-html="title"></h4>
            
            <a href="/downloads?type=import&key=classtimes" target="_blank" class="btn btn-default btn-primary">
                <i class="fa fa-download" aria-hidden="true"></i>下載範例檔
            </a> 
            
            
        </div> <!--  panel  heading -->
        <div  class="panel-body">
         
            <div class="row">
                <div class="col-sm-3"> 
                    <div class="form-group">
                        <select  v-model="center" class="form-control">
                            <option v-for="(item,index) in center_options" :key="index" :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                </div>       
                <div class="col-sm-9">
                    <button v-if="loading" class="btn btn-default">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                    </button> 
                    <label v-else  class="btn  btn-success btn-file" @click="init">
                       <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                       Excel 匯入
                       <input type="file"  ref="fileinput"  name="categorys_file" style="display: none;"  
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
        name: 'ClassTimeImport',
        props: {
            center_options:{
                type: Array,
                default: null
            },
        },
        data() {
            return {
                title:Helper.getIcon('classtimes')  + '  Excel 匯入上課時間',
                
                loading:false,

                files: [],

                center:0,

                err_msg:''
               
            }
        }, 
        computed:{
            hasError(){
                if(this.err_msg) return true
                    return false
            },
        },       
        mounted() {
             this.center=this.center_options[0].value
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
                    form.append('classtimes_file', this.files[i])
                     form.append('center', this.center)
                }

                let store=Classtime.import(form)
                store.then(result => {
                   
                        Helper.BusEmitOK()
                        this.loading=false
                        // this.$emit('imported')  
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