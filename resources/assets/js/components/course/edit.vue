<template>
<div>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4 v-html="title"></h4>  
             </span>   
             <button  v-if="!id" @click.prevent="onImportClicked"  class="btn btn-warning btn-sm" >
                  <span class="glyphicon glyphicon-import" aria-hidden="true"></span> 從舊課程匯入
             </button>     
        </div>
        <div class="panel-body">
            <form v-if="loaded" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div v-if="id"  class="col-sm-3">
                        <div class="text-center">
                            <photo :id="photo_id"></photo>
                            <h5>相片</h5>
                            <button  @click.prevent="editPhoto" title="編輯相片" class="btn btn-info btn-xs">                                 
                                <span class="glyphicon glyphicon-pencil"></span>
                            </button> 
                            <button v-show="photo_id" @click.prevent="onBtnDeletePhotoClicked" type="button" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="刪除相片">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button>
                            
                         </div>
                        
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>名稱</label>
                            <input type="text" name="course.name" class="form-control" v-model="form.course.name">
                            <small class="text-danger" v-if="form.errors.has('course.name')" v-text="form.errors.get('course.name')"></small>
                        </div>
                        <div class="form-group">  
                            <label>課程分類</label>
                             <drop-down :value.sync="categories" multiple  :options="categoryOptions" label="text"></drop-down>
                            <small class="text-danger" v-if="form.errors.has('course.categories')" v-text="form.errors.get('course.categories')"></small>
                        </div>
                       
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                          <label>開課中心</label>
                            <select @change="loadTeacherOptions"  v-model="form.course.center_id"  name="course.center_id" class="form-control" >
                                <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                        <div class="form-group">  
                            <label>教師</label>
                             <drop-down :value.sync="teachers" multiple  :options="teacherOptions" label="text"></drop-down>
                            <small class="text-danger" v-if="form.errors.has('course.teachers')" v-text="form.errors.get('course.teachers')"></small>
                        </div>
                       
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>學期別</label>
                            <select  v-model="form.course.term_id"  name="course.term_id" class="form-control" >
                                <option v-for="item in termOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                         </div>
                        <div class="form-group">
                           
                        </div>
                       
                        
                    </div>
                </div>
                <div class="row">
                    <div v-if="id" class="col-sm-3">
                       
                    </div>
                    <div class="col-sm-6">
                        
                        <div class="form-group">  
                            <label>課程簡介</label>
                            <textarea rows="6" cols="50" class="form-control" name="course.description"  v-model="form.course.description">
                            </textarea>
                            <small class="text-danger" v-if="form.errors.has('course.description')" v-text="form.errors.get('course.description')"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div v-if="id" class="col-sm-3">
                       
                    </div>
                    <div class="col-sm-3">
                        
                        <div class="form-group">  
                            <label>起始日期</label>
                            <div>
                                <date-picker :option="datePickerOption" :date="begin_date" ></date-picker>
                            </div>
                             <small class="text-danger" v-if="form.errors.has('course.begin_date')" v-text="form.errors.get('course.begin_date')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        
                        <div class="form-group">  
                            <label>結束日期</label>
                            <div>
                                <date-picker :option="datePickerOption" :date="end_date" ></date-picker>
                            </div>
                             <small class="text-danger" v-if="form.errors.has('course.end_date')" v-text="form.errors.get('course.end_date')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">  
                            <label>學分數</label>
                             <select  v-model="form.course.credit_count"  name="course.credit_count" class="form-control" >
                                <option v-for="item in creditCountOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                           
                    </div>
                </div>
                 <div class="row">
                    <div v-if="id" class="col-sm-3">
                       
                    </div>
                    
                    <div class="col-sm-3">
                     <div class="form-group">  
                            <label>週數</label>
                             <select  v-model="form.course.weeks"  name="course.weeks" class="form-control" >
                                <option v-for="item in weeksOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                       
                    </div>
                    <div class="col-sm-3">
                       <div class="form-group">  
                            <label>時數</label>
                            <input type="text" name="course.hours" class="form-control" v-model="form.course.hours">
                            <small class="text-danger" v-if="form.errors.has('course.hours')" v-text="form.errors.get('course.hours')"></small>
                        </div>
                       
                      
                    </div>
                     <div class="col-sm-3">
                       
                        
                        
                    </div>
                </div>
                 <div v-if="id" class="row">
                    <div class="col-sm-3">
                       
                    </div>
                    <div class="col-sm-3">
                        
                        <div class="form-group">  
                            <label>上架狀態</label>
                            <div>
                            <input type="hidden" v-model="form.course.active"  >
                                <toggle :items="activeOptions"   :default_val="form.course.active" @selected="setActive"></toggle>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-3">
                        
                        <div class="form-group">  
                            <label>審核</label>
                            <div>
                            <input type="hidden" v-model="form.course.reviewed"  >
                                <toggle :items="reviewedOptions"   :default_val="form.course.reviewed" @selected="setReviewed"></toggle>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-3">
                       
                      
                    </div>
                </div>
                <div class="row">

                    <div v-if="id" class="col-sm-3">
                      
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                      <button class="btn btn-default" @click.prevent="cancel">取消</button>
                    </div>

                </div>
                
            </form>
            
        </div>
    </div>


    <modal :showbtn="false" title="上傳圖片" :show.sync="imageUpload.show"  @closed="onImageUploadCanceled"
        effect="fade" width="800">
      
        <div slot="modal-body" class="modal-body">
            <image-upload :width="imageUpload.width" :height="imageUpload.height" 
               @uploaded="onPhotoUploaded">
            </image-upload>
        </div>
     </modal>
     
     <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeletePhotoCanceled" @confirmed="deletePhoto">        
    </delete-confirm>

    


</div>    
</template>
<script>
   
    export default {
        name: 'EditCourse',
        props: {
            id: {
              type: Number,
              default: 0
            },
        },
       
        data() {
            return {
                title:Helper.getIcon(Course.title()) ,
                loaded:false,

               
                form: new Form({
                   course:{}
                }),

                datePickerOption:Helper.datetimePickerOption(),
                begin_date: {
                    time: ''
                },
                end_date: {
                    time: ''
                },
                
                categories:[],
                teachers:[],
               
                centerOptions:[],
                categoryOptions: [],
                teacherOptions:[],   
                termOptions:[],
                creditCountOptions:Helper.numberOptions(0,15),
                weeksOptions:Course.weeksOptions(),
                activeOptions:Helper.activeOptions(),
                reviewedOptions:Helper.reviewedOptions(),

                photo_id: 0,
                imageUpload:{
                    show:false,
                    width:200,
                    height:200,
                    user:0,
                },

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }

            }
        },
        watch:{
            categories: function (val) {
               if(val.length){
                 this.clearErrorMsg('course.categories')
               }
            },
            teachers: function (val) {
               if(val.length){
                 this.clearErrorMsg('course.teachers')
               }
            },
            begin_date: {
              handler: function () {
                  this.form.course.begin_date=this.begin_date.time
                  this.clearErrorMsg('course.begin_date')
              },
              deep: true
            },
            end_date: {
              handler: function () {
                  this.form.course.end_date=this.end_date.time
                  this.clearErrorMsg('course.end_date')
              },
              deep: true
            },
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                if(this.id){
                    this.title +='  編輯課程資料'
                }else{
                    this.title +='  新增課程資料'
                }
                this.fetchData() 
                this.deleteConfirm={
                        id:0,
                        show:false,
                        msg:''
                 }

               
            },
            fetchData() {
                let id=this.id
                let getData=null
                if(id){
                    getData=Course.edit(id)
                }else{
                    getData=Course.create()
                }
                
               getData.then(data=>{
                    let course = data.course
                    this.form = new Form({
                            course: course,
                        })

                    this.begin_date.time=course.begin_date
                    this.end_date.time=course.end_date
                   
                    this.categories=Helper.getOptions(course.categories, 'id', 'name')
                    this.teachers=Helper.getOptions(course.teachers, 'user_id', 'name')

                    this.centerOptions=data.centerOptions
                    this.categoryOptions=data.categoryOptions
                    this.teacherOptions=data.teacherOptions
                    this.termOptions= data.termOptions

                    this.photo_id =Helper.tryParseInt(course.photo_id)

                    this.loaded=true


                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })           
                
                
            },
            loadTeacherOptions(){
                let center=this.form.course.center_id
                let params={ 
                    center:center
                }
                let options=Teacher.options(params)
                options.then(data => {
                    this.teachers=[]
                    this.teacherOptions=data.options
                })
                .catch(error=>{
                       Helper.BusEmitError(error) 
                    
                })     
            },     
            setActive(val) {
                this.form.course.active = val
            },
            setReviewed(val) {
                this.form.course.reviewed = val
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                this.form.course.teachers=this.teachers
                this.form.course.categories=this.categories
                this.submitForm()
            },
            submitForm() {
                let id=this.id
                let store=null
                if(id){
                    store=Course.update(this.form, id)
                }else{
                    store=Course.store(this.form)
                }
                store.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
            cancel(){
                this.$emit('canceled')
            },
            onImageUploadCanceled() {
                this.imageUpload.show=false
            },
            editPhoto() {
                this.imageUpload.show=true
            },
            onBtnDeletePhotoClicked(){
                this.deleteConfirm.msg='確定要刪除相片嗎？'
                this.deleteConfirm.show=true
            },
            onDeletePhotoCanceled(){
                this.deleteConfirm.show=false
            },
            deletePhoto() {
                this.updatePhoto(null)
                this.deleteConfirm.show=false
            },
            onPhotoUploaded(photo) {
                this.updatePhoto(photo) 
                this.imageUpload.show = false
            },
            updatePhoto(photo){
                let courseId = this.id
                let photoId = 0
                if(photo){
                    photoId = photo.id
                }

                let updatePhoto=Course.updatePhoto(courseId, photoId)
                updatePhoto.then(result => {
                    if(photo){
                        this.photo_id=photo.id
                    }else{           
                        this.photo_id=0
                    }
                })
                .catch(error => {
                    let title = '刪除相片失敗'
                    if(photo){
                        title = '更新相片失敗'
                    }
                    Helper.BusEmitError(error,title)                        
                })
            },
            onImportClicked(){
                this.$emit('import')
            }



        },

    }
</script>