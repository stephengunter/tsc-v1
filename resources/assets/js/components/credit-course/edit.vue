<template>
<div>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
            <span class="panel-title">
                 <h4 v-html="title"></h4>  
            </span>   
            <!-- <button  v-if="!id" @click.prevent="onImportClicked"  class="btn btn-warning btn-sm" >
                 <span class="glyphicon glyphicon-import" aria-hidden="true"></span> 從舊課程匯入
            </button>      -->
        </div>
        <div class="panel-body">
            <form v-if="loaded" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div v-if="id" class="col-sm-3">
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
                    <div v-bind:class="[ isCreate ? 'col-sm-12' : 'col-sm-9']">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">                           
                                    <label>名稱</label>
                                    <input type="text" name="course.name" class="form-control" v-model="form.course.name">
                                    <small class="text-danger" v-if="form.errors.has('course.name')" v-text="form.errors.get('course.name')"></small>
                                </div>
                            </div>  
                            <div class="col-sm-3">
                                <div class="form-group">                           
                                    <label>程度</label>
                                    <input type="text" name="course.level" class="form-control" v-model="form.course.level">
                                </div>
                                                        
                            </div> 
                            <div class="col-sm-3">
                                <div class="form-group">                           
                                    <label>開課中心</label>
                                    <select @change="onCenterChanged"  v-model="form.course.center_id"  name="course.center_id" class="form-control" >
                                        <option v-for="(item,index) in centerOptions" :key="index"  :value="item.value" v-text="item.text"></option>
                                    </select>
                                </div>
                                                        
                            </div> 
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>學期別</label>
                                    <select @change="onTermChanged"  v-model="form.course.term_id"  name="course.term_id" class="form-control" >
                                        <option v-for="(item,index) in termOptions" :key="index"  :value="item.value" v-text="item.text"></option>
                                    </select>
                                </div>
                                
                            </div>    
                        </div>  <!-- End Row   --> 
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">                           
                                    <label>群組課程</label>
                                    <div>
                                        <input type="hidden" v-model="form.course.group"  >
                                        <toggle :items="groupOptions"   :default_val="form.course.group" @selected="setGroup"></toggle>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-sm-4">
                                <div class="form-group">                           
                                    <label>父課程</label>
                                    <select :disabled="!isGroup" @change="onParentChanged" v-model="form.course.parent"  name="course.parent" class="form-control" >
                                        <option  v-for="(item,index) in parentOptions" :key="index" :value="item.value" v-text="item.text"></option>
                                    </select>
                                </div>
                                                        
                            </div> 
                            <div class="col-sm-4">
                                <div class="form-group">                           
                                    <label>學分班</label>
                                    <div>
                                        <input type="hidden" v-model="form.course.isCredit"  >
                                        <toggle :items="groupOptions"   :default_val="form.course.isCredit" @selected="setCreditCourse"></toggle>
                                    </div>
                                </div>
                                
                            </div>    
                        </div>  <!-- End Row   --> 
                        <div class="row">
                            <div v-if="!isCreate" class="col-sm-4">
                                 <div class="form-group">  
                                    <label>課程編號</label>
                                    <input type="text" name="course.number" class="form-control" v-model="form.course.number"  >
                                    <small class="text-danger" v-if="form.errors.has('course.number')" v-text="form.errors.get('course.number')"></small>
                                </div>
                            </div> 
                            <div class="col-sm-4">
                                 <div class="form-group">  
                                    <label>課程分類</label>
                                    <drop-down :value.sync="categories" multiple  :options="categoryOptions" label="text"></drop-down>
                                    <small class="text-danger" v-if="form.errors.has('course.categories')" v-text="form.errors.get('course.categories')"></small>
                                </div>
                            </div>  
                            <div class="col-sm-4">
                                <div  class="form-group">  
                                    <label>教師</label>
                                    <drop-down :value.sync="teachers" multiple  :options="teacherOptions" label="text">
                                        <slot name="no-options">daaa</slot>
                                    </drop-down>
                                    <small class="text-danger" v-if="form.errors.has('course.teachers')" v-text="form.errors.get('course.teachers')"></small>
                                </div>
                                                        
                            </div> 
                            <div class="col-sm-4">
                                
                                
                            </div>    
                        </div>  <!-- End Row   --> 
                        <div v-show="isCreditCourse" class="row">
                            <div class="col-sm-4">
                                <div class="form-group">   
                                    <label>學分數</label>
                                    <select @change="onCreditCountChanged"  v-model="form.course.credit_count"  name="course.credit_count" class="form-control" >
                                        <option v-for="(item,index) in creditCountOptions" :key="index" :value="item.value" v-text="item.text"></option>
                                    </select>
                                    <small class="text-danger" v-if="form.errors.has('course.credit_count')" v-text="form.errors.get('course.credit_count')"></small>
                                </div>
                            </div>  
                            <div class="col-sm-4">
                                <div  class="form-group">                           
                                    <label>必修</label>
                                    <div>
                                        <input type="hidden" v-model="form.course.must"  >
                                        <toggle :items="mustOptions"   :default_val="form.course.must" @selected="setMust"></toggle>
                                    </div>
                                </div>
                                                        
                            </div> 
                            <div class="col-sm-4">
                                <div class="form-group"> 
                                    <label>學分單價</label>
                                    <div>
                                        <input type="text" name="course.credit_price" class="form-control" v-model="form.course.credit_price">
                                        <small class="text-danger" v-if="form.errors.has('course.credit_price')" v-text="form.errors.get('course.credit_price')"></small>
                                    </div>
                                </div>
                            </div>    
                        </div>    
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">  
                                    <label>課程簡介</label>
                                    <textarea rows="4" cols="50" class="form-control" name="course.description"  v-model="form.course.description">
                                    </textarea>
                                    <small class="text-danger" v-if="form.errors.has('course.description')" v-text="form.errors.get('course.description')"></small>
                                </div> 
                            </div>    
                       
                        </div> <!-- End Row   --> 
                        <div  class="row">
                            <div class="col-sm-4">
                                <div class="form-group">  
                                    <label>起始日期</label>
                                    <div>
                                        <date-picker :option="datePickerOption" :date="begin_date" ></date-picker>
                                    </div>
                                    <small class="text-danger" v-if="form.errors.has('course.begin_date')" v-text="form.errors.get('course.begin_date')"></small>
                                </div>
                            </div> 
                            <div class="col-sm-4">
                                <div class="form-group">  
                                    <label>結束日期</label>
                                    <div>
                                        <date-picker :option="datePickerOption" :date="end_date" ></date-picker>
                                    </div>
                                    <small class="text-danger" v-if="form.errors.has('course.end_date')" v-text="form.errors.get('course.end_date')"></small>
                                </div>
                            </div> 
                        </div>      <!-- End Row   -->  
                        <div  class="row">
                            <div class="col-sm-4">
                                <div  class="form-group">  
                                    <label>週數</label>
                                    <select  v-model="form.course.weeks"  name="course.weeks" class="form-control" >
                                        <option v-for="(item,index) in weeksOptions" :key="index" :value="item.value" v-text="item.text"></option>
                                    </select>
                                </div>
                            </div> 
                            <div class="col-sm-4">
                                <div class="form-group">  
                                    <label>時數</label>
                                    <input type="text" name="course.hours" class="form-control" v-model="form.course.hours">
                                    <small class="text-danger" v-if="form.errors.has('course.hours')" v-text="form.errors.get('course.hours')"></small>
                                </div>
                            </div> 
                            
                        </div>      <!-- End Row   --> 
                        <!-- <div v-if="!isCreate" v-show="form.course.canReview" class="row">
                            <div class="col-sm-4">
                                <div  class="form-group">  
                                    <label>狀態</label>
                                    <div>
                                        <input type="hidden" v-model="form.course.active"  >
                                        <toggle :items="activeOptions"   :default_val="form.course.active" @selected="setActive"></toggle>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-sm-4">
                                <div class="form-group">  
                                    <label>審核</label>
                                    <div>
                                        <input type="hidden" v-model="form.course.reviewed"  >
                                        <toggle :items="reviewedOptions"   :default_val="form.course.reviewed" @selected="setReviewed"></toggle>
                                    </div>
                                </div>
                            </div> 
                            
                        </div>      -->
                        <div  class="row">
                            <div class="col-sm-12">
                                <div class="form-group">  
                                    <label>注意事項</label>
                                    <textarea rows="4" cols="50" class="form-control" name="course.caution"  v-model="form.course.caution">
                                    </textarea>
                                    <small class="text-danger" v-if="form.errors.has('course.caution')" v-text="form.errors.get('course.descripcautiontion')"></small>
                                </div> 
                               
                            </div>
                        </div> 
                        <div  class="row">
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button class="btn btn-default" @click.prevent="cancel">取消</button>
                            </div>
                        </div>      <!-- End Row   -->     
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
            parent: {
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

                parentCourse:null,
                groupCourses:[],
                groupOptions: Helper.boolOptions(),
                mustOptions: Helper.boolOptions(),

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
                parentOptions:[],
                teacherOptions:[],   
                termOptions:[],
                creditCountOptions:Helper.numberOptions(0,60),
                weeksOptions:Course.weeksOptions(),
                activeOptions:[{
                    text: '正常開課',
                    value: '1'
                }, {
                    text: '停止開課',
                    value: '0'
                }],
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
            categories(val) {
               if(val.length){
                 this.clearErrorMsg('course.categories')
               }
            },
            teachers (val) {
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
        computed:{
            isCreate(){
                return !Helper.isTrue(this.id) 
            },
            isCreditCourse(){
                return Helper.isTrue(this.form.course.isCredit)  
            },
            isGroup(){
                return Helper.isTrue(this.form.course.group)  
            },
            hasParent(){
                return Helper.isTrue(this.form.course.parent)
            },
            hideTeacher(){
                return this.groupAndParent
            },
            groupAndParent(){
                return this.isGroup && !this.hasParent
            }

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

                this.parentCourse=null
            },
            fetchData() {
                let id=this.id
                let getData=null
                if(id){
                    getData=Course.edit(id)
                }else{
                    getData=Course.create(this.parent)
                }
                
                getData.then(data=>{
                    let course = data.course
                    course.caution = Helper.replaceAll(course.caution, '<br>', '\n')
                    this.form = new Form({
                            course: course,
                        })
                        
                    this.setCreditPrice(course.credit_price)


                    this.begin_date.time=course.begin_date
                    this.end_date.time=course.end_date
                   
                    this.categories=Helper.getOptions(course.categories, 'id', 'name')
                    this.teachers=Helper.getOptions(course.teachers, 'user_id', 'name')

                    this.centerOptions=data.centerOptions
                    this.categoryOptions=data.categoryOptions
                    this.teacherOptions=data.teacherOptions
                    this.termOptions= data.termOptions

                    if(data.groupCourses){
                        this.groupCourses=data.groupCourses
                    }

                    if(data.groupOptions){
                        this.parentOptions=data.groupOptions
                    }

                    this.photo_id =Helper.tryParseInt(course.photo_id)

                    this.loaded=true


                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })           
                
                
            },
            onCenterChanged(){
                this.loadTeacherOptions()
                this.loadParentOptions()
            },
            onTermChanged(){
                 this.loadParentOptions()
            },
            onCreditCountChanged(){
                this.clearErrorMsg('course.credit_count')
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
            setCreditPrice(val){
                if(!val){
                    if(this.parentCourse && this.parentCourse.credit_price){
                         val= this.parentCourse.credit_price
                    }else{
                        val=''
                    }
                } 

                this.form.course.credit_price=Helper.formatMoney(val)
               
            },
            setCreditCourse(val){
                this.form.course.isCredit=val
                this.clearErrorMsg('')
            }, 
            setGroup(val){
                this.form.course.group=val
             
                if(Helper.isTrue(val)){
                    this.loadParentOptions()                    
                }

                this.clearErrorMsg('')
            },
            setMust(val){
                this.form.course.must = val
            },
            loadParentOptions(){
                
                if(!this.isGroup) return false
                let center=this.form.course.center_id
                let term=this.form.course.term_id
                let params={ 
                    center:center,
                    term:term
                }
                let options=Course.groupOptions(params)
                options.then(data => {
                    this.parentOptions=data.options
                    this.groupCourses=data.groupCourses
                })
                .catch(error=>{
                       Helper.BusEmitError(error) 
                    
                })   
            },
            onParentChanged(){
                if(!this.isCreate) return 

                this.parentCourse=this.groupCourses.find(item=>{
                    return item.id == this.form.course.parent
                })

                this.setCreditPrice()
             
            },
            setActive(val) {
                this.form.course.active = val
            },
            setReviewed(val) {
               
                this.form.course.reviewed = val
            },
            clearErrorMsg(name) {
                
                if(name) this.form.errors.clear(name)
                else this.form.reset()
                
            },
            onSubmit() {
                this.form.course.teachers=this.teachers
                this.form.course.categories=this.categories

                if(this.form.course.caution){
                   let caution=Helper.replaceAll( this.form.course.caution, '\n','<br>')
                   this.form.course.caution=caution
                }
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
                store.then(course => {

                    Helper.BusEmitOK()
                    this.$emit('saved',course)                            
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