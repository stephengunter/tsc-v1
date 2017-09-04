<template>

    <div v-if="loaded" class="panel panel-default show-data">
        
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                   
                    <label class="label-title">Email主旨</label>
                    <p>{{mail.title}}</p>
                    
                </div>
                
           </div>  <!-- End row-->
            <div class="row">
                <div class="col-sm-12">
                    <label class="label-title">Email內容</label>
                    <p v-html="mail.content"></p>
                    
                </div>
                
           </div>  <!-- End row-->
            <div class="row" v-if="hasFiles">
                <div class="col-sm-12">
                    <label class="label-title">
                        附件檔案
                    </label>
                    <p v-for="file in mail.files">{{ file.title }}</p>
                    
                </div>
                
           </div>  <!-- End row-->
            <div class="row">
                <div class="col-sm-12">
                   <label class="label-title">最後更新</label>
                   <updated :entity="mail"></updated>
                    
                </div>
                
            </div>  <!-- End row-->
       
        </div>  <!-- End panel-body-->

    </div>

    
 

</div>
</template>
<script>
    export default {
        name: 'MailContent', 
        props: {
            notice_id: {
              type: Number,
              default: 0
            },
        },
        data() {
             return {
               
                loaded:false,
                mail:null,
                files:[],

            }
        },
        beforeMount() {
            this.init()
        },  
        computed: {
            hasFiles() {
                if(!this.mail) return false
                if(!this.mail.files)   return false
                 return this.mail.files.length > 0
            },
        },
        methods: {    
            init(){
                this.loaded=false
                this.mail=null
                this.files=[]
                if(this.notice_id) this.fetchData()
               
            },
            fetchData() {
                let url='/notices-email?notice=' + this.notice_id
                axios.get(url)
                .then(response => {
                    this.mail = response.data

                    this.loaded=true
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            },   
            showUpdatedBy(){
               let updated_by=Helper.tryParseInt(this.mail.updated_by)
               if(updated_by){
                  Bus.$emit('onShowEditor',updated_by)
               }
                
            },
            
        }
    }
</script>