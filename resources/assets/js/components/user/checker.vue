<template>
<form  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form">
    <div class="row">
       <div :class="colStyle">
            <div class="form-group"> 
              <label>姓名</label>
              <input type="text" name="user.profile.fullname" class="form-control" v-model="form.user.profile.fullname" >
              <small v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')" class="text-danger"></small>
            </div>  
       </div>
       <div :class="colStyle">
            <div class="form-group"> 
              <label>Email</label>
              <input type="text" name="user.email" class="form-control" v-model="form.user.email" >
              <small v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')" class="text-danger"></small>
            </div> 
       </div>
       <div class="col-sm-4"> 
           <div class="form-group">                          
              <label>手機</label>
             
              <div class="form-inline">
                  <input type="text" name="user.phone" class="form-control" v-model="form.user.phone" >
                  &nbsp;&nbsp;
                  <button @click.prevent="onCheckUserClicked" v-show="!checkedTimes" :disabled="form.hasError()" class="btn btn-success btn-sm">
                       {{ button_text }}
                  </button>
                  <small v-if="form.errors.has('user.data')" v-text="form.errors.get('user.data')" class="text-danger" ></small>
                
              </div>
              <small v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')" class="text-danger" >身分證號</small>
                 
            </div>
       </div>
    </div>
</form>    
</template>

<script>
    export default {
        name: 'UserChecker',
        props: {
            button_text:{
               type: String,
               default: '下一步'
            },
            version: {
              type: Number,
              default: 0
            },
            status:{
               type: Number,
               default: 0
            },
            col_width:{
               type: Number,
               default: 3
            }
        },
        data() {
            return {
                colStyle:'col-sm-3',
                user_checked:false,
                checkedTimes:0,
                form : new Form({
                    user:{
                       email:'',
                       phone:'',
                       profile:{
                          fullname:'',
                       }
                    }
                }),
                userList:[]
            }
        },
        watch: {
            version: function () {
                this.init()
            },
            status: function () {
                this.onCheckUserClicked()
            }
        },
        beforeMount() {
           this.init()
        },
        methods:{
            init(){
                this.colStyle='col-sm-' + this.col_width
                this.form = new Form({
                    user:{
                       email:'',
                       phone:'',
                       profile:{
                          fullname:'',
                       }
                    }
                })
              
            },
            onCheckUserClicked(){

               let check= this.checkUser()
               check.then(result=>{
                   if(result){
                      let user=this.form.user
                      this.$emit('checked',user)
                      this.user_checked=true
                      this.checkedTimes+=1
                   }else{
                      this.$emit('check-failed',this.userList)
                      this.user_checked=false
                   }
                   
               }).catch(error =>{
                      Helper.BusEmitError(error)
                      this.$emit('check-failed',this.userList)
                      this.user_checked=false
               })
            },
            checkUserInput(){
                return new Promise((resolve, reject) => {
                   let errors={}
                   let user=this.form.user
                   let profile=user.profile
                   if(!profile.fullname) {
                      errors['user.profile.fullname']=['必須填寫姓名']
                   }
                   if(!user.phone) {
                      errors['user.phone']=['必須填寫手機號碼']
                   }
                   
                   this.form.onFail(errors)
                  
                   if(!this.form.errors.any()){
                      resolve(true) 
                   }else{
                      reject()
                   } 
                 
               }) 
            },
            checkUser(){
               return new Promise((resolve, reject) => {
                   this.userList=[]

                   let inputChecked=this.checkUserInput()

                   inputChecked.then(result=>{
                       let user=this.form.user
                       let email=user.email
                       let phone=user.phone
                       let findUsers=User.findUsers(email,phone)
                       findUsers.then(data => {
                                     this.userList= data.userList   
                                     if(this.userList.length){
                                         this.showUserList=true
                                         let  errors={}
                                         errors['user.data']=['相同資料的使用者已經存在']
                                         this.form.onFail(errors)
                                         resolve(false)
                                     }else{
                                         this.user_checked=true
                                         this.showUserList=false
                                         resolve(true)
                                     }                 
                              }).catch(error => {
                                  if(error.status==422){
                                      this.form.onFail(error.data)
                                      resolve(false)
                                  }else{
                                    Helper.BusEmitError(error) 
                                      resolve(false)
                                  }
                              })

                     }).catch(error => {
                         reject(false)
                     })
                 
               }) 
            },
            hasUserError(){
                let form=this.form
                if(form.errors.has('user.fullname')) return true
                if(form.errors.has('user.phone')) return true
                if(form.errors.has('user.email')) return true
                return false
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
                if(name=='user.email' || name=='user.phone' ){
                     this.form.errors.clear('user.data')
                }

            },
        }, 
    }
</script>