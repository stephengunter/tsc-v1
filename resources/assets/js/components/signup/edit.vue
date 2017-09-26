<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4 v-html="title"></h4>  
             </span>           
        </div>
        <div class="panel-body">
            <form v-if="loaded" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form-horizontal">
                  <div class="form-group">
                      <label  class="col-sm-2 control-label">報名日期</label>
                      <div class="col-sm-4">
                         <div>
                            <div>
                                <date-picker :option="datePickerOption" :date="date" ></date-picker>
                            </div>
                            <input type="hidden" name="signup.date" class="form-control" v-model="signup.date"  >
                            <small class="text-danger" v-if="form.errors.has('signup.date')" v-text="form.errors.get('signup.date')"></small>

                         </div>
                         
                      </div>
                      <label  class="col-sm-2 control-label">網路報名</label>
                       <div class="col-sm-4">
                           <div>
                               <toggle :items="boolOptions"   :default_val="signup.net_signup" @selected=setNetSignup></toggle>
                           </div>
                       </div>
                    
                  </div>
                  <div class="form-group">
                      <label  class="col-sm-2 control-label">姓名</label>
                      <div class="col-sm-4">
                         <div>
                              <input class="form-control" type="text"  :value="user.profile.fullname" disabled>
                         </div>
                      </div>

                      <label  class="col-sm-2 control-label">報名課程</label>
                      <div class="col-sm-4">
                         <div>
                              <input class="form-control" type="text"  :value="courseName" disabled>
                         </div>
                      </div>
                      
                    
                  </div>
                  <div v-if="user" class="form-group">
                      <label  class="col-sm-2 control-label">性別</label>
                      <div class="col-sm-4">
                         <div>
                            <input class="form-control" type="text"  :value="$options.filters.genderText(user.profile.gender)" disabled>
                         </div>
                      </div>

                      <label  class="col-sm-2 control-label">生日</label>
                      <div class="col-sm-4">
                         <div>
                            <input class="form-control" type="text"  :value="user.profile.dob" disabled>
                         </div>
                      </div>
                  </div>
                  <div v-if="user"  class="form-group">
                      <label  class="col-sm-2 control-label">身分證號</label>
                      <div class="col-sm-4">
                         <div>
                            <input class="form-control" type="text"  :value="user.profile.SID" disabled>
                         </div>
                      </div>

                      <label  class="col-sm-2 control-label">電話</label>
                      <div class="col-sm-4">
                         <div>
                            <input class="form-control" type="text"  :value="user.phone" disabled>
                         </div>
                      </div>
                  </div>
                  <div class="form-group" >
                       <label  class="col-sm-2 control-label">折扣優惠</label>
                       <div class="col-sm-10">
                           <div>
                               <toggle :items="discountOptions"   :default_val="discount_id" @selected=setDiscount></toggle>
                           </div>
                       </div>
                  </div>
                  <div class="form-group" >
                       <label  class="col-sm-2 control-label">請選擇課程</label>
                       <div class="col-sm-10">
                          <sub-course-selector :courses="subCourses"
                        :show_submit="selectorSettings.show_submit" :version="selectorSettings.version"
                         :default_selected="selectorSettings.default_selected"
                         @submit-courses="onSubCourseSelected" >
                       </sub-course-selector>
                       </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確定</button>
                        <small class="text-danger" v-if="subCourseError" >請選擇課程</small>
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button class="btn btn-default" @click.prevent="canceled">取消</button>
                    </div>
                  </div>
            </form>
        </div>
    </div>
    
</template>
<script>
    import SubCourseSelector from '../../components/course/sub/selector.vue'

    export default {
        name: 'EditSignup',
        components: {
             'sub-course-selector':SubCourseSelector
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                title:Helper.getIcon('Signups')  + '  編輯報名紀錄',
                loaded:false,
                signup:{},

                user:{},
                course:{},
                courseName:'',
                discount_id:0,
               
                form: new Form({
                   signup:{}
                }),
                 boolOptions:Helper.boolOptions(),
                datePickerOption:{},
                date: {
                    time: ''
                },

                selectorSettings:{
                   default_selected:[],
                   show_submit:false,
                   version:0,
                },
                subCourses:[],
                subCourseError:false

            }
        },
        watch:{
            date: {
              handler: function () {
                  this.signup.date=this.date.time
                  this.clearErrorMsg('signup.date')
              },
              deep: true
            },
        },
        computed: {
            needSelect(){
                return this.subCourses.length > 0  
            },
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                this.fetchData() 

                this.datePickerOption=Helper.datetimePickerOption()
 
            },
            fetchData() {
                let getData=Signup.edit(this.id)  
                   getData.then(data=>{
                       this.setSignup(data)

                       this.course = data.course
                       this.courseName=Course.getFormatedCourseName(this.course,true)
                       
                       this.user = data.user

                       this.loadDiscountOptions(data.discountOptions)

                       this.loaded=true

                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })           
                
                
            },
            setSignup(data){
               let signup=data.signup
               this.subCourses=signup.subCourses
               let selected_sub_courses=[]
               for(let i=0; i<signup.subCourses.length;i++){
                   selected_sub_courses.push(signup.subCourses[i].id)
               }
               this.selectorSettings.default_selected=selected_sub_courses
                    


               this.date.time=signup.date
               this.discount_id=Helper.tryParseInt(signup.discount_id)

               this.signup=signup
            },
            setNetSignup(val){
                this.signup.net_signup=val
            }, 
            loadDiscountOptions(options){
                this.discountOptions=options
                let noDiscount={ text:'無' , value:'0' }
                this.discountOptions.splice(0, 0, noDiscount);
            },      
            setDiscount(val) {
                this.signup.discount_id = val;
            },  
            onSubCourseSelected(selectedIds){
              alert('onSubCourseSelected')
                this.signup.sub_courses=selectedIds
                if(selectedIds.length){
                   this.subCourseError=false
                   this.submitForm()
                }else{
                    this.subCourseError=true
                }
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                if(this.needSelect){

                  this.selectorSettings.version +=1
                }else{
                  this.submitForm()
                }
               
            },
            submitForm() {
              alert('submit')
              return false
                this.form = new Form({
                   signup:this.signup
                })

                let update=Signup.update(this.form , this.id)
                
                update.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
         
           
            canceled(){
                this.$emit('canceled')
            }




        },

    }
</script>