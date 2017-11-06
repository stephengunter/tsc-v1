<template>
    
    <div class="panel panel-default">
        <div class="panel-heading ">
            <h4 v-html="title"></h4>
            
            <a :href="downloadUrl" target="_blank" class="btn btn-default btn-primary">
                <i class="fa fa-download" aria-hidden="true"></i>下載範例檔
            </a> 
        </div> <!--  panel  heading -->
        
        <div class="panel-body">
         
            <div class="row">
                <div class="col-sm-4">
                    <toggle :items="isUpdateOptions"   :default_val="0" @selected="setIsUpdate"></toggle>
                </div>
                <div class="col-sm-4">
                    <toggle v-show="isCreate" :items="groupOptions"   :default_val="0" @selected="onTypeSelected"></toggle>
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
    
    import CourseSelector from '../../components/course/selector.vue' 
    export default {
        name: 'CourseImport',
        components: {
            'course-selector':CourseSelector
        },
        props: {
            
           
        }, 
        data() {
            return {
                title:Helper.getIcon('Courses')  + '  Excel 匯入課程',
                
                loading:false,

                group:0,
                isUpdate:0,

                files: [],

                err_msg:'',

                groupOptions:[{
                    text: '一般課程',
                    value: '0'
                }, {
                    text: '群組課程',
                    value: '1'
                }],

                isUpdateOptions:[{
                    text: '建立新課程',
                    value: '0'
                }, {
                    text: '更新課程資料',
                    value: '1'
                }]


               
            }
        },
        computed:{
            isGroup(){
               return Helper.isTrue(this.group)
            },
            isCreate(){
               return !Helper.isTrue(this.isUpdate)
            },
            hasError(){
                if(this.err_msg) return true
                    return false
            },
            downloadUrl(){
                let url='/downloads?type=import&key='
                if(this.isGroup) {
                    return  url+'group-courses'
                }else{
                    if(this.isCreate)  return  url+'courses'
                    else return  url+'course-infoes'
                }
                
            }
        
        },
        mounted(){
            this.init()
        },
        methods: {
            init(){
                this.err_msg=''
                this.loading=false
            },
            
            onTypeSelected(val){
                this.group=val              
            },
            setIsUpdate(val){
                this.isUpdate=val
               
            },
            onFileChange(e) {
              
                var files = e.target.files || e.dataTransfer.files
                if (!files.length)  return
                   
                this.files = e.target.files

                this.submitExcelImport()
            },
            submitExcelImport() {
                this.loading=true
               
                let form = new FormData()
                for (let i = 0; i < this.files.length; i++) {
                    form.append('courses_file', this.files[i])
                    form.append('group', this.group)
                    form.append('update', this.isUpdate)
                }

                let store=CourseImport.store(form)
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