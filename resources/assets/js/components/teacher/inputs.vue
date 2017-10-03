<template>

<div v-if="form">
   <div class="row" v-if="with_user">
       <div  class="col-sm-4"> 
           <div class="form-group"> 
              <label>姓名</label>
              <input type="text" name="user.profile.fullname" class="form-control" v-model="form.user.profile.fullname" >
              <small v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')" class="text-danger"></small>
            </div> 
       </div>  
       <div  class="col-sm-4"> 
            <div class="form-group"> 
              <label>Email</label>
              <input type="text" name="user.email" class="form-control" v-model="form.user.email" >
              <small v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')" class="text-danger"></small>
            </div> 
       </div>
       <div  class="col-sm-4"> 
           <div class="form-group"> 
              <label>手機</label>
              <input type="text" name="user.phone" class="form-control" v-model="form.user.phone" >
              <small v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')" class="text-danger"></small>
            </div> 
       </div>
   </div>
   <div class="row" v-if="with_profile">
       <div  class="col-sm-4"> 
           <div class="form-group">                          
              <label>性別</label>
               <div>
                  <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected=onGenderSelected></toggle>
                </div>
           </div>
       </div>  
       <div  class="col-sm-4"> 
           <div class="form-group">                          
              <label>身分證號</label>
              <input type="text" name="user.profile.SID" class="form-control" v-model="form.user.profile.SID" >
              
              <small v-if="form.errors.has('user.profile.SID')" v-text="form.errors.get('user.profile.SID')" class="text-danger" >身分證號</small>
                 
            </div>
       </div>
       <div  class="col-sm-4"> 
           <div class="form-group">
                <label>生日</label>
                <!-- <div>
                    <input type="hidden" name="user.profile.dob" v-model="form.user.profile.dob">
                    <date-picker  :date="dob" :option="datePickerOption"></date-picker>
                </div> -->
                <div class="input-group">
                    <date-picker  :date="dob" :option="datePickerOption"></date-picker>
                    <span v-show="hasDOB" class="input-group-btn">
                        <button @click.prevent="clearDOB" class="btn btn-default" type="button">
                            <i class="fa fa-times-circle-o" aria-hidden="true"></i>
                        </button>
                    </span>
                </div>
                <input type="hidden" name="user.profile.dob" v-model="form.user.profile.dob">
                                
                <small class="text-danger" v-if="form.errors.has('user.profile.dob')" v-text="form.errors.get('user.profile.dob')"></small>
            </div>
       </div>
   </div>
   <div class="row">
      <div v-if="with_teacher_name" class="col-sm-4">
          <div class="form-group">                           
              <label>姓名</label>
              <input type="text" name="teacher.name" class="form-control" v-model="form.teacher.name" disabled >
          </div>
      </div>
      <div class="col-sm-4">
          <div class="form-group">
              <label>狀態</label>
              <div>
                  <toggle :items="activeOptions"   :default_val="form.teacher.active" @selected=onActiveSelected></toggle>
              </div>
          </div>
      </div>
      <div class="col-sm-4">
          <div class="form-group">
              <label>資料審核</label>
              <div>
              <input type="hidden" v-model="form.teacher.reviewed"  >
              <toggle :items="reviewedOptions"   :default_val="form.teacher.reviewed" @selected=onReviewedSelected></toggle>
              </div>
          </div>
      </div>
   </div>   <!--  row   -->
   <div class="row">
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>專長</label>
                <input type="text" name="teacher.specialty" class="form-control" v-model="form.teacher.specialty"  >
                <small class="text-danger" v-if="form.errors.has('teacher.specialty')" v-text="form.errors.get('teacher.specialty')"></small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>最高學歷</label>
                <input type="text" name="teacher.education" class="form-control" v-model="form.teacher.education"  >
                <small class="text-danger" v-if="form.errors.has('teacher.education')" v-text="form.errors.get('teacher.education')"></small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>教師證書號</label>
                <input type="text" name="teacher.certificate" class="form-control" v-model="form.teacher.certificate"  >
                <small class="text-danger" v-if="form.errors.has('teacher.certificate')" v-text="form.errors.get('teacher.certificate')"></small>
            </div>
         </div>
   </div> <!--  row   -->
   <div class="row">
       <div class="col-sm-4">
          <div class="form-group">                           
              <label>現職</label>
              <input type="text" name="teacher.job" class="form-control" v-model="form.teacher.job"  >
              <small class="text-danger" v-if="form.errors.has('teacher.job')" v-text="form.errors.get('teacher.job')"></small>
          </div>
       </div>
       <div class="col-sm-4">
            <div class="form-group">                           
                <label>職稱</label>
                <input type="text" name="teacher.jobtitle" class="form-control" v-model="form.teacher.jobtitle"  >
                <small class="text-danger" v-if="form.errors.has('teacher.jobtitle')" v-text="form.errors.get('teacher.jobtitle')"></small>
            </div>
        </div>
        <div class="col-sm-4">
            
        </div>
   </div> <!--  row   -->
   <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label>學經歷</label>
                <textarea rows="6" cols="50" class="form-control" name="teacher.experiences"  v-model="form.teacher.experiences">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.experiences')" v-text="form.errors.get('teacher.experiences')"></small>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group">
                <label>個人簡介</label>
                <textarea rows="6" cols="50" class="form-control" name="teacher.description"  v-model="form.teacher.description">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></small>
            </div>
        </div>
        <div class="col-sm-4">
           
        </div>

   </div>  <!-- end row-->
   <div class="row">
       <div class="col-sm-4">
           <div class="form-group">                           
               <button type="submit"  class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               <button type="button" class="btn btn-default" @click.prevent="onCanceled">取消</button>  
           </div>
        </div>
      
   </div>
</div>  
</template>


<script>
  export default {
      name: 'TeacherInputs',
      props: {
            form: {
               type: Object,
               default: null
            },
            with_teacher_name:{
               type:Boolean,
               default:false
            },
            with_user:{
               type:Boolean,
               default:false
            },
            with_profile:{
               type:Boolean,
               default:false
            }
      },
      data() {
            return {
                dob:{
                   time:''
                },
                dobError:false,
                genderOptions:Helper.genderOptions(),
                datePickerOption: Helper.datetimePickerOption(),
                activeOptions:Helper.activeOptions(),
                reviewedOptions:Helper.reviewedOptions()
            }
      },
      computed:{
          hasTeacher(){
           
              if(!this.form) return false
              if(this.form.teacher.user_id) return true
                  return false
          },
          hasDOB(){
              if(this.form.user.profile.dob) return true
              return false
          }
        
      },
      watch:{
            dob: {
              handler: function () {

                  this.$emit('dob-selected',this.dob.time)
                  
              },
              deep: true
            },
      },
      beforeMount() {
           this.init()
      },
      methods: {
          init() {
              if(this.form.user){
                 this.dob.time=this.form.user.profile.dob
              }
          },
          onGenderSelected(val){
             this.$emit('gender-selected',val)
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
          clearDOB(){
                this.dob.time=''
          },
      }
  }
</script>