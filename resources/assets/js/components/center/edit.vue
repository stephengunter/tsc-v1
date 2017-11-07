<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">           
            <span class="panel-title">
                   <h4 v-html="title"></h4>                  
            </span>           
        </div>
        <div v-if="loaded" class="panel-body">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
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
                                    <input type="text" name="center.name" class="form-control" v-model="form.center.name">
                                    <small class="text-danger" v-if="form.errors.has('center.name')" v-text="form.errors.get('center.name')"></small>
                                </div>
                            </div>  
                            <div class="col-sm-4">
                                <div class="form-group">                           
                                    <label>代碼</label>
                                    <input type="text" name="center.code" class="form-control" v-model="form.center.code">
                                    <small class="text-danger" v-if="form.errors.has('center.code')" v-text="form.errors.get('center.code')"></small>
                                </div>                                                            
                            </div> 
                            <div class="col-sm-4">
                                <div class="form-group">                           
                                    <label>課程洽詢電話</label>
                                    <input type="text" name="center.course_tel" class="form-control" v-model="form.center.course_tel">
                                    <small class="text-danger" v-if="form.errors.has('center.course_tel')" v-text="form.errors.get('center.course_tel')"></small>
                                </div>                                                            
                            </div> 
                                
                        </div>   <!-- End Row   --> 
                        <div v-if="isCreate" class="row">
                            <div class="col-sm-4">
                                <div class="form-group">                           
                                    <label>電話</label>
                                    <input type="text" name="contact_info.tel" class="form-control" v-model="form.contact_info.tel">
                                    <small class="text-danger" v-if="form.errors.has('contact_info.tel')" v-text="form.errors.get('contact_info.tel')"></small>
                                </div>
                            </div>  
                            <div class="col-sm-4">
                                <div class="form-group">                           
                                    <label>傳真</label>
                                    <input type="text" name="contact_info.fax" class="form-control" v-model="form.contact_info.fax">
                                    <small class="text-danger" v-if="form.errors.has('contact_info.fax')" v-text="form.errors.get('contact_info.fax')"></small>
                                </div>                                                            
                            </div> 
                                
                        </div>   <!-- End Row   -->
                        <div  class="row">
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button class="btn btn-default" @click.prevent="endEdit">取消</button>
                            </div>
                        </div>      <!-- End Row   -->    
                    </div>    
                    
                </div><!-- End Row   --> 
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
        name: 'EditCenter',
        props: {
            id: {
              type: Number,
              default: 0
            }
        },
        data() {
            return {
                title:Helper.getIcon('Centers'),
              
                loaded:false,
               
                form: {},
                
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
        computed:{
            isCreate(){
                return this.id == 0
            },

        }, 
        beforeMount() {
            this.init()
        },
        methods: {
            
            init(){
                if(this.isCreate){

                    this.title +=  '  新增開課中心資料'

                }else{

                    this.title += '  編輯開課中心資料'
                    this.deleteConfirm={
                        id:0,
                        show:false,
                        msg:''
                    }
                }
                
                
                this.fetchData()
              
            },
          
            fetchData() {
               
                let getData=null

                if(this.isCreate)   getData=Center.create()                   
                else  getData=Center.edit(this.id)

                getData.then(data => {
                    let center = data.center
                    if(this.isCreate){
                       
                        this.form = new Form({
                            center: center,
                            contact_info:data.contact_info
                        })

                    }else{
                        this.form = new Form({
                            center: center,
                        })
                    }

                    this.photo_id =Helper.tryParseInt(center.photo_id)

                    this.loaded=true

                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)                   
                    this.loaded=false
                })
                
            },
            
            onSubmit() {
                
                let save=null

                if(this.isCreate) save=Center.store(this.form)                   
                else save=Center.update(this.form , this.id)
                
                save.then(center => {
                      
                       this.$emit('saved',center)
                       Helper.BusEmitOK('資料已存檔')
                    })
                    .catch(error => {
                       Helper.BusEmitError(error,'存檔失敗')
                    })
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
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
                let centerId = this.form.center.id
                let photoId = 0
                if(photo){
                    photoId = photo.id
                }

                let updatePhoto=Center.updatePhoto(centerId, photoId)
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
            endEdit(){
              
                this.$emit('canceled')
            }




        },

    }
</script>