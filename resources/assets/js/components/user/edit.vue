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
                    <div class="col-sm-3">
                        <div class="text-center">
                            <photo :id="photo_id"></photo>
                            <h5>個人相片</h5>
                            <button @click.prevent="editPhoto(1)" title="編輯相片" class="btn btn-info btn-xs">                                 
                                <span class="glyphicon glyphicon-pencil"></span>
                            </button> 
                            <button v-show="photo_id" @click.prevent="editPhoto(0)" type="button" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="刪除相片">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button>
                            
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>真實姓名</label>
                            <input type="text" name="user.profile.fullname" class="form-control" v-model="form.user.profile.fullname"  >
                            <small class="text-danger" v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')"></small>
                        </div>
                        <div class="form-group">
                            <label>身分證號</label>
                            <input type="text" name="user.profile.SID" class="form-control" v-model="form.user.profile.SID"  >
                             <small class="text-danger" v-if="form.errors.has('user.profile.SID')" v-text="form.errors.get('user.profile.SID')"></small>
                        </div>
                        <div class="form-group">
                            
                            <label>Email</label>
                            <input type="text" name="user.email" class="form-control" v-model="form.user.email" >
                            
                            <small class="text-danger" v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')"></small>
                        </div>
                       
                        <div class="form-group">
                            <label>手機</label>
                            
                            <input type="text" name="user.phone" class="form-control" v-model="form.user.phone" >
                               
                            
                            <small class="text-danger"  v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')"></small>
                        </div>
                    </div>
                     <div class="col-sm-3">
                        
                        <div class="form-group">
                            <label>性別</label>
                            <div>
                             <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected="setGender"></toggle>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>生日</label>
                            <div class="input-group">
                                <date-picker  :date="dob" :option="dateOption"></date-picker>
                                <span v-show="hasDOB" class="input-group-btn">
                                    <button @click.prevent="clearDOB" class="btn btn-default" type="button">
                                        <i class="fa fa-times-circle-o" aria-hidden="true"></i>
                                    </button>
                                </span>
                            </div>
                          
                            <input type="hidden" name="user.profile.dob" class="form-control" v-model="form.user.profile.dob"  >
                            <small class="text-danger" v-if="form.errors.has('user.profile.dob')" v-text="form.errors.get('user.profile.dob')"></small>
                        </div>
                         <div class="form-group">
                            <label>使用者名稱</label>
                            <input type="text" name="user.name" class="form-control" v-model="form.user.name"  >
                             <small class="text-danger" v-if="form.errors.has('user.name')" v-text="form.errors.get('user.name')"></small>
                        </div>
                        
                    </div>
                    <div class="col-sm-3">
                        
                        <div class="form-group">
                            <label>稱謂</label>
                            <select  v-model="form.user.profile.title_id"  name="user.profile.title_id" class="form-control" >
                                <option v-for="(item,index) in titleOptions" :key="index" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                    </div>
                    <div class="col-sm-4">
                        <button type="submit"  class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                      <button class="btn btn-default" @click.prevent="endEdit">取消</button>
                    </div>

                </div>
                    
                
            </form>
        </div>   <!-- end panel-body -->
    </div>  <!-- end panel -->
    
    <photo-editor :user_id="photoEditorSettings.user_id" 
        :entity_type="photoEditorSettings.entity_type" :entity_id="photoEditorSettings.entity_id"
        :action="photoEditorSettings.action" :show="photoEditorSettings.show"
        @canceled="onCancelEditPhoto" @photo-updated="onPhotoUpdated"
        @photo-update-failed="onPhotoUpdateFailed">

    </photo-editor>
        
    
     
</div>
</template>
<script>
    export default {
        name: 'EditUser',
        props: {
            id: {
              type: Number,
              default: 0
            },
            role:{
               type: String,
               default: 'User'
            }  
        },
        data() {
            return {
                loaded:false,

                title:Helper.getIcon('Users')  + '  編輯使用者資料',

                form: new Form({
                    user: {
                        profile: {}
                    }

                }),

                photoEditorSettings:{
                    user_id:0,
                    entity_type:'user',
                    entity_id:this.id,
                    action:'upload',
                    show:false
                },
               
                
              
                photo_id: 0,
                gender: 1,
                genderOptions:[],
                titleOptions:[],
               
                dob: {
                    time: ''
                },

                dateOption: {},
              

            }
        },
        computed:{
            hasDOB(){
                if(this.form.user.profile.dob) return true
                    return false
            }

        },
        watch:{
            id:function(){
                this.init()
            },
           
            dob: {
              handler: function () {
                  this.form.user.profile.dob=this.dob.time
                  this.clearErrorMsg('user.profile.dob')                 
              },
              deep: true
            },
        },
        
        beforeMount() {
            this.init()
        },
        methods: {
            init(){
               
                this.genderOptions= Helper.genderOptions()
                this.dateOption= Helper.datetimePickerOption()
                this.fetchData()
              
            },
            fetchData() {

                let getUser=User.edit(this.id)
                getUser.then(data => {
                    this.titleOptions = data.titleOptions
                    let user = data.user
                    this.form = new Form({
                            user: user,
                        })
                    this.dob.time = user.profile.dob

                    this.photo_id = Helper.tryParseInt(user.profile.photo_id)

                    this.loaded=true

                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)                   
                    this.loaded=false
                })
                
            },
            endEdit(){
              
                this.$emit('canceled')
            },
            onSubmit() {
                
                this.form.user.role = this.role
                
                let id=this.form.user.id
                let updateData=User.update(this.form , id, this.role)
                updateData.then(user => {
                       this.$emit('saved',user)
                       Helper.BusEmitOK('資料已存檔')
                    })
                    .catch(error => {
                       Helper.BusEmitError(error,'存檔失敗')
                    })
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            clearDOB(){
                this.dob.time=''
            },
            setGender(val) {
                this.form.user.profile.gender = val
            },
            //photoe functions
            editPhoto(val){
                if(val){
                    this.photoEditorSettings.action='upload'
                    this.photoEditorSettings.show=true
                }else{
                    this.photoEditorSettings.action='delete'
                    this.photoEditorSettings.show=true
                }
            },
            onCancelEditPhoto() {
                this.photoEditorSettings.show=false
            }, 
            onPhotoUpdated(photoId){
                this.onCancelEditPhoto()
                this.photo_id=Helper.tryParseInt(photoId)
                let msg = '刪除相片成功'
                if(photoId){
                    msg = '更新相片成功'
                    
                }
                Helper.BusEmitOK(msg)
            },
            onPhotoUpdateFailed(photoId){
                this.onCancelEditPhoto()

                let title = '刪除相片失敗'
                if(photoId){
                    title = '更新相片失敗'
                }
                Helper.BusEmitError(error,title)    
            },
            




        },

    }
</script>