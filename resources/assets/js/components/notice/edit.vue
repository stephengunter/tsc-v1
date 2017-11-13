<template>
<div> 

    <div class="panel panel-default">
        <div class="panel-heading">           
            <span class="panel-title">
                <h4 v-html="title"></h4>  
            </span>   
               
        </div>
        <div class="panel-body">
            <form v-if="loaded" class="form-horizontal" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="form-group">
                    <label  class="col-sm-2 control-label">標題</label>
                    <div class="col-sm-10">
                       <input type="text" class="form-control" name="notice.title" v-model="form.notice.title">
                       <small class="text-danger" v-if="form.errors.has('notice.title')" v-text="form.errors.get('notice.title')"></small>

                    </div>
                </div>
                <div class="form-group">
                    <label  class="col-sm-2 control-label">內容</label>
                    <div class="col-sm-10">
                        <small class="text-danger" v-if="form.errors.has('notice.content')" v-text="form.errors.get('notice.content')"></small>

                        <html-editor :model="form.notice.content"
                        :version="textEditor.version"
                        @html-value="setContent">
                            
                        </html-editor>
                    
                    </div>
                </div>
                <div v-if="id" class="form-group">
                    <label  class="col-sm-2 control-label">狀態</label>
                    <div class="col-sm-10">
                        <input type="hidden" v-model="form.notice.active"  >
                        <toggle :items="activeOptions"   :default_val="form.notice.active" @selected="setActive"></toggle>
                        
                    </div>
                </div>
                <div  class="form-group">
                    <label  class="col-sm-2 control-label">發佈到網站</label>
                    <div class="col-sm-2">
                        <input type="hidden" v-model="form.notice.public"  >
                        <toggle :items="publicOptions"  :default_val="form.notice.public" @selected="setPublic"></toggle>
                    
                    </div>
                </div>
                <div v-if="canEditDate"   class="form-group">
                    <label  class="col-sm-2 control-label">日期</label>
                    <div class="col-sm-10">
                         <date-picker :option="datePickerOption" :date="date" ></date-picker>
                    </div>
                </div>
                <div v-if="!id" class="form-group">
                    <label  class="col-sm-2 control-label">Email給學員與教師</label>
                    <div class="col-sm-2">
                        <input type="hidden" v-model="form.notice.emails"  >
                        <input type="hidden" v-model="form.notice.courses"  >
                        <toggle :items="publicOptions"   :default_val="form.notice.emails" @selected=setEmails></toggle>
                       
                    </div>
                    <div  class="col-sm-8">
                        <small class="text-danger" v-if="form.errors.has('notice.courses')" v-text="form.errors.get('notice.courses')"></small>

                        <p v-show="form.notice.emails">
                            <a href="#" @click.prevent="beginSelectCourses" >
                               <span v-for="course  in selectedCourses">
                                   {{ course.number }} &nbsp; {{ course.name }}
                               </span>
                            </a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label  class="col-sm-2 control-label">附件檔案</label>
                    <div class="col-sm-2">
                        <file-upload @file-uploaded="onFileUploaded">
                        </file-upload>
                    </div>
                    <div  class="col-sm-8">
                       <table v-show="hasFiles" class="table"  style="font-size:17px; width:75%">
                           <tr v-for="(file,index) in files">
                               <td style="width:10%"> 
                                    <button class="btn btn-danger btn-xs" @click.prevent="removeFile(index)">
                                       <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </button>
                                </td>
                               <td> {{ file.title }}</td>
                           </tr>
                       </table>
                    </div>
                </div>
                <div class="form-group">
                    <label  class="col-sm-2 control-label"></label>
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-default" @click.prevent="onCanceled">取消</button>  
                    </div>
                </div>
            </form>
        </div> 
    </div>  

    <modal :showbtn="selectorSettings.showBtn"  :show.sync="selectorSettings.showing" @closed="onSelectorCanceled" 
        effect="fade" :width="selectorSettings.width">
         
        <div v-if="!id" slot="modal-body" class="modal-body">
            <course-selector
              :selected_ids="selectedCourseIds"
              :show_title="selectorSettings.show_title"
              :title="selectorSettings.title"
              :default_text="selectorSettings.default_text"
             
              @submit="onSelectorSubmit"
              @canceled="onSelectorCanceled"  >
                
            </course-selector>
        </div>
    </modal>

</div>        
</template>
<script>
    import CourseFullSelector from '../../components/course/full-selector.vue'

    export default {
        name: 'EditNotice',
        components: {
           'course-selector':CourseFullSelector
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                title:Helper.getIcon(Notice.title()),
                loaded:false,
               
                form: new Form({
                   notice:{},
                   attachments:[]
                }),

                textEditor:{
                    version:0
                },
                activeOptions: Helper.activeOptions(),

                publicOptions: Helper.boolOptions(),

                datePickerOption:Helper.datetimePickerOption(),
                date:{
                   time: ''
                },
                selectorSettings:{
                    width:1200,
                    showBtn:false,
                    showing:false,
                    show_title:false,
                    title:'',
                    default_text:'請選擇要Email通知的課程'
                },

                selectedCourseIds:[],
                selectedCourses:[],

           
                files: [],

            }
        },
        computed:{
            canEditDate(){
                return Helper.isTrue(this.form.notice.public)
            },
            hasFiles(){
                return this.files.length > 0
            }
        },
        mounted() {
            this.init()
        },
        watch:{
            date: {
              handler: function () {
                  this.form.notice.date=this.date.time
              },
              deep: true
            },
        },
        methods: {
            init() {
                this.loaded=false
                if(this.id){
                    this.title +='  編輯公告'
                }else{
                    this.title +='  新增公告'
                }

                this.files=[]

                this.fetchData() 
            },
            fetchData() {
                
                let id=this.id
                let getData=null
               
                if(id){
                    getData=Notice.edit(id)
                }else{
                    getData=Notice.create()
                }
                
               getData.then(data=>{
                        let notice = data.notice
                        if(data.files){
                             this.files=data.files
                        }

                       
                        this.form = new Form({
                                notice: notice,
                                attachments:[]
                            })
                        this.date.time=notice.date

                        
                        this.loaded=true


                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })           
                
                
            },
            setActive(val) {
                this.form.notice.active = val
            },
            setPublic(val) {

                this.form.notice.public = val
            },
            setEmails(val){
                
                if( Helper.isTrue(val)){
                    this.beginSelectCourses()
                    this.form.notice.emails=1
                }else{
                    this.form.notice.emails=0
                    this.clearErrorMsg('notice.courses')
                }
            },
            beginSelectCourses(){
                this.selectorSettings.showing=true
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                this.textEditor.version+=1
            },
            setContent(val){
                this.form.notice.content=val
                this.submitForm()
            },
            onFileUploaded(file){
                this.files.push(file)
            },
            removeFile(index){
                this.files.splice(index, 1);
            },
            submitForm() {
                let id=this.id
                let errors={}
                if(!id && this.form.notice.emails) {
                    if(!this.selectedCourseIds || !this.selectedCourseIds.length){
                        errors['notice.courses']=['請選擇要Email通知的課程']
                    }else{
                        this.form.notice.courses=Helper.arrayToSplit(this.selectedCourseIds)
                    }
                }
               this.form.onFail(errors)
               if(this.form.errors.any()){
                
                  return false
               }

                this.form.attachments=this.files
                
                let store=null
                if(id){
                    store=Notice.update(this.form, id)
                   
                }else{
                    store=Notice.store(this.form)
                }
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
            },
            onSelectorSubmit(selectedIds,selectedCourses){
                this.selectedCourseIds=selectedIds
                this.selectedCourses=selectedCourses

                if(this.selectedCourseIds && this.selectedCourseIds.length)
                {
                    this.clearErrorMsg('notice.courses')
                }

                this.selectorSettings.showing=false
            },
            onSelectorCanceled(){
                this.selectorSettings.showing=false
            }
           
            



        },

    }
</script>