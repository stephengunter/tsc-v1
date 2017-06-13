<template>
<div v-if="loaded">
    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4><i class="fa fa-pencil-square-o" aria-hidden="true"></i>  {{ title }}</h4>  
             </span>           
        </div>
        <div class="panel-body">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-3">
                        <div id="coursePhoto" v-show="canEditPhoto" class="text-center">
                            <img v-show="photo.path" :src="photo.path"  class="img-thumbnail  profile-img" alt="個人相片" >
                             <h5>相片</h5>
                            <button @click.prevent="editPhoto" title="編輯相片" class="btn btn-info btn-xs">                                 
                                <span class="glyphicon glyphicon-pencil"></span>
                            </button> 
                            <button v-show="showDeletePhotoBtn()" @click.prevent="showConfirm=true" type="button" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="刪除相片">
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
                            <label>狀態</label>
                            <div>
                            <input type="hidden" v-model="form.course.active"  >
                             <toggle :items="activeOptions"   :defaultVal="form.course.active" @selected="setActive"></toggle>
                            </div>
                        </div>
                       
                        
                    </div>
                </div>
             

                <div class="row">
                    <div class="col-sm-3">
                       
                    </div>
                    <div class="col-sm-3">
                     <div class="form-group">  
                            <label>學分數</label>
                             <select  v-model="form.course.credit_count"  name="course.credit_count" class="form-control" >
                                <option v-for="item in creditCountOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                       
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
                <div class="row">
                    <div class="col-sm-3">
                       
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
                        
                      
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                      
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                      <button class="btn btn-default" @click.prevent="endEdit">取消</button>
                    </div>

                </div>
                    
                
            </form>
        </div>
    </div>

     <modal :showbtn="false" title="上傳圖片" :show.sync="showModal"  @closed="closeModel"
        effect="fade" width="800">
      
        <div slot="modal-body" class="modal-body">
            <image-upload :width="200" :height="200"   @uploaded="photoUploaded"></image-upload>
        </div>
     </modal>
     <modal showbtn="true"  :show.sync="showConfirm" @ok="deletePhoto"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
          <div slot="modal-header" class="modal-header modal-header-danger">
            <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
        </div>
        <div slot="modal-body" class="modal-body">
            <h3> 確定要刪除相片？</h3>
        </div>
     </modal>
     
</div>
</template>
<script>
    export default {
        name: 'EditCourse',
        props: ['id'],
        components: {
            'drop-down':DropDown,
            
            'toggle': Toggle,
            'modal': Modal,
            'image-upload': ImageUpload,
            'date-picker' : MyDatepicker
        },
        data() {
            return {
                title:'新增課程資料',
                loaded:false,
                canEditPhoto:false,
                showModal: false,

                showConfirm: false,
                form: new Form({
                    course: {}
                }),
                begin_date: {
                    time: ''
                },
                end_date: {
                    time: ''
                },
                photo: {},

                activeOptions: [{
                    text: '上架中',
                    value: '1'
                }, {
                    text: '已下架',
                    value: '0'
                }],
               
                
                

                categories:[],
                teachers:[],
               
                centerOptions:[],
                categoryOptions: [],
                teacherOptions:[],   
                creditCountOptions:[],
                termOptions:[],
                datePickerOption:{},

            }
        },
        watch: {
            id:function(){
                this.init()
            },
            
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
        },
        beforeMount() {          
            this.init()
        },
       
        methods: {
            init(){
                this.categories=[]
                this.teachers=[]
                this.form = new Form({
                    course:{}
                })
               
                this.fetchData() 

                if(this.id){
                    this.title="編輯課程資料"
                    this.canEditPhoto=true
                }else{
                    this.title="新增課程資料"
                }
                this.creditCountOptions=Helper.numberOptions(0,15)
                this.weeksOptions=Helper.numberOptions(1,30)
                this.datePickerOption=Helper.datetimePickerOption()
            },            
            fetchData() {
                let id=this.id               
                let url = '/api/courses/'  
                 if(!id){
                    url += 'create'
                 } else{
                    url += id + '/edit';
                 }        
                axios.get(url)
                    .then(response => {
                        let course = response.data.course
                        this.form.course=course

                        this.begin_date.time=course.begin_date
                        this.end_date.time=course.end_date
                        this.categories=course.categories
                        this.teachers=course.teachers

                        this.centerOptions=response.data.centerOptions
                        this.categoryOptions=response.data.categoryOptions
                        this.teacherOptions=response.data.teacherOptions
                        this.termOptions= response.data.termOptions
                       

                        if(this.canEditPhoto){
                            let photo_id = course.photo_id
                             this.getPhoto(photo_id)
                        }

                        this.loaded=true

                        this.$emit('courseLoaded')
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            loadTeacherOptions(){
                let center=this.form.course.center_id
                let url = '/api/teachers/optionsByCenter/' + center;
                axios.get(url)
                    .then(response => {
                        this.teachers=[]
                         this.teacherOptions=response.data.options
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            getPhoto(photo_id) {
                let url = '/api/photoes/';
                if (photo_id) {
                    url += photo_id
                } else {
                    url += 'defaultCourse'
                }

                axios.get(url)
                    .then(response => {
                        this.photo = response.data.photo
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
           
            setActive(val) {
                this.form.course.active = val;
            },
            
            
            setValues(){
                 this.form.course.begin_date=this.begin_date.time
                 this.form.course.end_date=this.end_date.time
                 
            },
            onSubmit() {
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.submitForm()
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
            },
            submitForm() {
                this.setValues()

                let url = '/api/courses';
                let method='post'
                let id=this.id
                if(id){
                    method='put'
                    url += '/' + id 
                } 
               
                this.form.submit(method,url)
                    .then(response => {
                        Helper.BusEmitOK()
                        this.$emit('saved',response.course)
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                    })
            },
            showDeletePhotoBtn() {
                if (this.photo.id) return true
                return false

            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            closeModel() {
                this.showModal = false
            },
            closeConfirm() {
                this.showConfirm = false
            },
            editPhoto() {
                this.showModal = true
            },
            
            photoUploaded(photo) {                
                this.updatePhoto(photo)
                this.showModal = false
            },
            deletePhoto() {
                this.updatePhoto()
                this.showConfirm = false
            },
            updatePhoto(photo){
               
                let photoForm = new Form({
                    photo_id: ''
                })
                if(photo){
                    photoForm.photo_id=photo.id
                }
                
                let course = this.form.course.id
                let url = '/api/courses/' + course + '/updatePhoto'

                photoForm.put(url)
                    .then(result => {
                        if(photo){
                            this.form.course.photo_id=photo.id
                            this.getPhoto(photo.id)

                            this.$emit('photoChanged',photo.id)
                        }else{
                            this.form.course.photo_id=null                          
                            this.getPhoto()
                            
                            this.$emit('photoChanged',null)
                        }


                    })
                    .catch(error => {
                      
                        let msgtitle = '刪除相片失敗'
                        if(photo){
                            msgtitle = '更新相片失敗'
                        }
                        Helper.BusEmitError(error,msgtitle)
                       
                    })
            },
            endEdit(){
                this.$emit('endEditCourse')
            },





        },

    }
</script>