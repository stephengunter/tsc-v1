<template>

    <div class="panel panel-default">
        <div class="panel-heading ">           
       

            <h4 v-html="title"></h4>
            
            <a href="/downloads?type=import&key=centers" target="_blank" class="btn btn-default btn-primary">
                <i class="fa fa-download" aria-hidden="true"></i>下載範例檔
            </a> 
            
            
        </div> <!--  panel  heading -->
        <div  class="panel-body">
         
            <div class="row">
                
                <div class="col-sm-4">
                     <label  class="btn  btn-success btn-file" @click="reset">
                       <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                       Excel 匯入
                       <input type="file"  ref="fileinput"  name="centers_file" style="display: none;"  
                       @change="onFileChange" >
                    </label>
                </div>
            </div>
            

        </div> <!--  panel  body -->
       
    </div>  <!--  panel  -->

   
</template>

<script>
    
    export default {
        name: 'CenterImport',
        
        data() {
            return {
                title:Helper.getIcon('centers')  + '  Excel 匯入開課中心',
                
                loaded:false,

                type:true,

                files: [],

               


               
            }
        },
        
        beforeMount() {
             
        },
        methods: {
            reset(){
               this.$refs.fileinput.value = null
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
                

                let form = new FormData()
                for (let i = 0; i < this.files.length; i++) {
                    form.append('centers_file', this.files[i])
                    
                }

                let store=Center.import(form)
                store.then(result => {
                        Helper.BusEmitOK()
                        this.$emit('imported')  
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'存檔失敗')
                    })
            },
           
            
            
        },

    }
</script>