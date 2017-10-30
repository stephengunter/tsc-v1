<template>
<div  v-if="readOnly">
    <div class="row">
        <div class="col-sm-3">
             <photo :id="$options.filters.tryParseInt(teacher.user.profile.photo_id)"></photo>
        </div>
        <div class="col-sm-3">
            <label  class="label-title">名稱</label>
            <p v-text="teacher.name"></p>  
        </div> 
        <div class="col-sm-3">
            <label class="label-title">所屬中心</label>
            <p>
                <span v-html="$options.filters.namesText(teacher.centerNames)"></span>
            </p> 
        </div>   
        <div class="col-sm-3">
            <label class="label-title">資料審核</label>
            <p v-if="hasReviewedBy" >
                <a @click.prevent="showReviewedBy" href="#" v-html="$options.filters.reviewedLabel(teacher.reviewed)">                         
                </a>
                &nbsp;     
                <button v-if="teacher.canReview" class="btn btn-primary btn-xs" @click.prevent="editReview" >
                    <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                </button>  
            </p>
            <p v-else > 
                <span v-html="$options.filters.reviewedLabel(teacher.reviewed)"></span>     
                &nbsp;        
                <button class="btn btn-primary btn-xs" @click.prevent="editReview" >
                    <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                </button>             
            </p>
        </div>     
    </div>   <!--  row   -->   
    <div class="row">
        <div class="col-sm-3">
             
        </div>
        <div class="col-sm-9">
            <label class="label-title">簡介</label>
            <p v-text="teacher.description"></p>          
        </div> 
          
    </div>   <!--  row   -->    
    <div class="row">
        <div class="col-sm-3">
             
        </div>
        <div class="col-sm-4">
            <label class="label-title">建檔日期</label>
            <p>{{ teacher.created_at | tpeTime  }}</p> 
        </div> 
        <div class="col-sm-5">
            <label class="label-title">最後更新</label>
            <p v-if="!teacher.updated_by"> {{   teacher.updated_at|tpeTime  }}</p>
            <p v-else>
                <a  href="#" @click.prevent="showUpdatedBy" >
                    {{   teacher.updated_at|tpeTime  }}
                </a>
                
            </p> 
        </div>     
    </div>   <!--  row   -->  
</div> 
<div v-else>
    <div class="row">
        <div class="col-sm-3">
            <div v-if="!isCreate" class="text-center">
                <photo :id="photo_id"></photo>
                <h5>相片</h5>
                <button @click.prevent="editPhoto" title="編輯相片" class="btn btn-info btn-xs">                                 
                    <span class="glyphicon glyphicon-pencil"></span>
                </button> 
                <button v-show="photo_id" @click.prevent="showConfirm=true" type="button" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="刪除相片">
                    <span class="glyphicon glyphicon-trash"></span>
                </button>
                
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group">                           
                <label>名稱</label>
                <input type="text" name="teacher.name" class="form-control" v-model="form.teacher.name"  >
                <small class="text-danger" v-if="form.errors.has('teacher.name')" v-text="form.errors.get('teacher.name')"></small>
       
            </div>
            <div class="form-group">
                <label>簡介</label>
                <textarea rows="6" cols="50" class="form-control" name="teacher.description"  v-model="form.teacher.description">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></small>
            </div>
        </div>
        
    </div>   <!--  row   --> 
    <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-4">
            <div class="form-group">                           
                <button type="submit"  class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-default" @click.prevent="onCanceled">取消</button>  
           </div>
        </div>
      
    </div>

    <modal :showbtn="false" title="上傳圖片" :show.sync="showModal"  @closed="closeModel"
        effect="fade" width="800">
      
        <div slot="modal-body" class="modal-body">
            <image-upload :width="200" :height="200" :user="form.teacher.user_id" @uploaded="photoUploaded"></image-upload>
        </div>
    </modal>
    <modal showbtn="true"  :show.sync="showConfirm" @ok="deletePhoto"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
          <div slot="modal-header" class="modal-header modal-header-danger">
            <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
        </div>
        <div slot="modal-body" class="modal-body">
            <h3> 確定要刪除個人相片？</h3>
        </div>
    </modal>     
</div>  

</template>


<script>
   export default {
        name: 'TeacherGroupInputs',
        props: {
            form: {
               type: Object,
               default: null
            },
            teacher: {
               type: Object,
               default: null
            },
        },
        data() {
            return {
                photo_id: 0,
                showModal:false,
                showConfirm: false,
                activeOptions:Helper.activeOptions(),
            }
        },
        computed:{
            readOnly(){
               if(this.teacher) return true
               return false
            },
            hasReviewedBy(){
                if(!this.teacher) return false
                if(!this.teacher.reviewed_by) return false
                return parseInt(this.teacher.reviewed_by)
            },
            isCreate(){
                if(!this.form.teacher) return false
                if(!this.form.teacher.user_id) return true
                return false
            }
        
        },
        beforeMount() {
           this.init()
        },
        methods: {
            init() {
                this.showModal=false
                this.showConfirm=false
                if(!this.readOnly && !this.isCreate){
                    let teacher=this.form.teacher
                    this.photo_id=Helper.tryParseInt(teacher.user.profile.photo_id)
                } 
                
            },
            onActiveSelected(val){
                this.$emit('active-selected',val)
            },
            
            onReviewedSelected(val){
                this.$emit('reviewed-selected',val)
            },
            onCanceled(){
                this.$emit('canceled')
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',parseInt(this.teacher.updated_by))
            },
            showReviewedBy(){
                Bus.$emit('onShowEditor', parseInt(this.teacher.reviewed_by) , '審核者')
            },
            editReview(){
                this.$emit('edit-review')
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
            deletePhoto() {
                this.updatePhoto()
                this.showConfirm = false
            },
            photoUploaded(photo) {
                this.updatePhoto(photo)   
                         
                this.showModal = false
            },
            updatePhoto(photo){
                let userId = this.form.teacher.user_id
                let photoId = 0
                if(photo){
                    photoId = photo.id
                }

                let updateUserPhoto=User.updateUserPhoto(userId, photoId)
                updateUserPhoto.then(result => {
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
      }
  }
</script>