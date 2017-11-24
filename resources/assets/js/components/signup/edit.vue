<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">           
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>  
                    
        </div>
        <div class="panel-body">
            <form v-if="loaded"  @submit.prevent="onSubmit" class="form">
               <div class="row">
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>報名日期</label>
                            <div>
                                <date-picker :option="datePickerOption" :date="date" ></date-picker>
                            </div>
                            <input type="hidden" name="signup.date" class="form-control" v-model="form.signup.date"  >
                            <small v-if="form.errors.has('signup.date')" v-text="form.errors.get('signup.date')" class="text-danger"></small>
                        </div>  
                   </div>
                   <div class="col-sm-3">
                        <div class="form-group"> 
                           <label>姓名</label>
                           <input class="form-control" type="text"  :value="studentName()" disabled>
                        </div>
                        
                   </div>
                   <div class="col-sm-6">
                        <div class="form-group"> 
                            <label>課程</label>
                           
                            <input class="form-control" type="text"  :value="courseName()" disabled>
                            
                        </div>  
                   </div>
                   
               </div>
               
               
                <!-- <div v-if="needSelect" class="row">
                   <div class="col-sm-12" >
                       <sub-course-selector :courses="subCourses"
                        :show_submit="selectorSettings.show_submit" :version="selectorSettings.version"
                         :default_selected="selectorSettings.default_selected"
                         @submit-courses="onSubCourseSelected" >
                       </sub-course-selector>
                   
                   </div> 
                </div>  -->
                <pay-inputs  :form="form" 
                   @discount-selected="onDiscountSelected" @clear-error="clearErrorMsg">
                </pay-inputs> 
                
               
               
                <div class="row" >
                    <div  class="col-sm-6">
                        <button type="submit"  class="btn btn-success" :disabled="!canSubmit">確定</button>
                        
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
    import PayInputs from './pay-inputs.vue'
    export default {
        name: 'EditSignup',
        components: {
            'sub-course-selector':SubCourseSelector,
             'pay-inputs':PayInputs
        },
        props: {
            id:{
               type: Number,
               default: 0
            },
            
        },
        data() {
            return {
                loaded:false,
                title:Helper.getIcon('Signups')  + '  編輯報名表',
               

                subCourses:[],
                
                course:'',
                user:{},
               
                form: new Form({
                   signup:{},
                   
                }),

               
                

                datePickerOption:{},
                date: {
                    time: ''
                },
               
                selectorSettings:{
                   default_selected:[],
                   show_submit:false,
                   version:0,
                },

                subCourseError:false,

                
            }
        },
        watch: {
            
            date: {
              handler: function () {
                  this.form.signup.date=this.date.time
                  this.clearErrorMsg('signup.date')
              },
              deep: true
            },
            
    
        },
        computed: {
            canSubmit(){
                return true
            },
            needSelect(){
                return this.subCourses.length > 0  
            },
            
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.fetchData() 
                this.datePickerOption=Helper.datetimePickerOption()

            },
            fetchData() {
                let getData=Signup.edit(this.id)
                     
                getData.then(data => {
                    let signup=data.signup
                    signup.cost=Helper.formatMoney(signup.cost,true)
                   
                    //this.subCourses=data.subCourses
                    
                    //this.selectorSettings.default_selected=signup.sub_courses
                    
                    this.date.time=signup.date
                    this.form=new Form({
                        signup:signup,
                    })

                    this.course=data.course
                    this.user=data.user

                    this.payways=data.payways

                    this.loaded=true
                   
                })
                .catch(error=> {
                    Helper.BusEmitError(error)                        
                })
                
            },
            studentName(){
                return this.user.profile.fullname
            },
            courseName(){
                return Course.getFormatedCourseName(this.course)
            },
            onSubCourseSelected(selectedIds){
                this.form.signup.sub_courses=selectedIds
                if(selectedIds.length){
                   this.subCourseError=false
                   this.submitForm()
                }else{
                    this.subCourseError=true
                }
            },
            onDiscountSelected(discount){
                this.form.signup.discount_id=discount.id
                this.form.signup.tuition=Helper.formatMoney(discount.tuition,true)
                
            },
            onSubmit() {
                this.submitForm()
                
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            submitForm() {
               
                
                let save=Signup.update(this.form, this.id)
                
                save.then(data => {

                    Helper.BusEmitOK()
                    this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
            canceled(){
                this.$emit('canceled')
            },
        },

    }
</script>