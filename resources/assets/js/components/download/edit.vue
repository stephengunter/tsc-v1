<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">    
            
            <span class="panel-title">
                
                <h4>
                    <i class="fa fa fa-download" aria-hidden="true"></i>

                    {{ title}}
                </h4>
            </span>   
            
        </div>
        <div  v-if="ready"  class="panel-body">  
            <form class="form" @submit.prevent="onSubmit" >
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">                           
                            <label>標題</label>
                            <input type="text" name="download.title" class="form-control" 
                            v-model="download.title"  @keydown="clearErrorMsg('title')">
                            <small class="text-danger" v-if="hasError('title')" v-text="getErrorMsg('title')"></small>
                        </div>
                    </div>
                    <div v-show="!download.name" class="col-sm-4">
                        <div class="form-group">                           
                            <label>檔案</label>
                            <input name="filedata"  type="file"  ref="fileinput"   
                                @change="onFileChange" >
                            <small class="text-danger" v-if="hasError('filedata')" v-text="getErrorMsg('filedata')"></small> 
                        </div>
                    </div>
                    <div v-show="download.name" class="col-sm-4">
                        <div class="form-group">                           
                            <label>檔案</label>
                            <p>
                                {{ download.name }}

                                &nbsp;   &nbsp; 
                                <button class="btn btn-danger btn-xs" @click.prevent="clearFile">
                                    <span aria-hidden="true" class="glyphicon glyphicon-remove"></span>
                                </button>

                            </p>
                            
                        </div>
                    </div>
                    
                </div>
                 
                <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group">                           
                            <button type="submit" class="btn btn-success" >確認送出</button>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <button class="btn btn-default" @click.prevent="onCanceled">取消</button>  
                        </div>
                    </div>
                    
                </div><!-- </div> end row -->
                    
                
            </form>
           
        </div>
    </div>
    
</template>
<script>
    
    export default {
        name: 'EditDownload',
        props: {
            id: {
              type: Number,
              default: 0
            },
            
        },
        data() {
            return {
                title:'',
                ready:false,
               
                download: {},

                errors:{ title:'' , filedata:''}  
                
                
            }
        },
        
        beforeMount() {
            this.init()
           
        },
        methods: {
            init() {
                
                this.ready=false
                this.err_msg=''

                this.download= {}

                if(this.id){
                    this.title='  編輯文件下載'
                }else{
                    this.title='  新增文件下載'
                }
                this.fetchData() 
            },
            fetchData() {
                let getData=null
                if(this.id){
                    getData=Download.edit(this.id)
                }else{
                    getData=Download.create(this.course_id)
                }
                getData.then(data=>{
                    this.download=data.download

                    this.ready=true
                }).catch(error=>{
                   Helper.BusEmitError(error)  
                   this.ready=false
                })  
            },
            clearFile(){
                this.download.name=null
                this.$refs.fileinput.value = null
            },  
            hasError(name){
                
                if(!this.errors.hasOwnProperty(name)) return false
                if(this.errors[name]){
                    return true
                }else{
                    return false
                }
            },
            getErrorMsg(name){
                if(!this.hasError(name)) return ''
                return this.errors[name]
            },
            onFileChange(e) {
              
                var files = e.target.files || e.dataTransfer.files
                if (!files.length)  return
                   
                this.files = e.target.files
                
                this.clearErrorMsg('filedata')
               
            },
            clearErrorMsg(name) {
              
               if(!this.hasError(name)) return 
               this.errors[name]=''
            },
            onSubmit() {
                let err=false
                let title=this.download.title.trim()
                if(!title){
                    this.errors.title='請填寫標題'
                    err=true
                }

                if(!this.files && !this.download.name){
                    
                    this.errors.filedata='請上傳檔案'
                    err=true
                }

                if(!err) this.submitForm(title)
               
                
            },
            submitForm(title) {
                let form = new FormData()
                form.append('id', this.id)
                form.append('title', title)
                if(this.files){
                    for (let i = 0; i < this.files.length; i++) {
                    
                        form.append('filedata', this.files[i])
                    
                    }
                }
                

                let store=Download.store(form)
               
                store.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
            onCanceled(){
                this.$emit('canceled')
            }




        },

    }
</script>