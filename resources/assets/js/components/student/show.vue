<template>
    <div v-if="loaded" class="panel panel-default show-data">
      <div class="panel-heading">
          <span class="panel-title">
              <h4 v-html="title"></h4>
          </span>    
          <div>
              <button v-show="can_back"  @click="onBtnBackClick" class="btn btn-default btn-sm" >
                 <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                 返回
              </button>
              <button  v-if="student.canEdit" v-show="can_edit" @click="btnEditClicked" class="btn btn-primary btn-sm" >
                  <span class="glyphicon glyphicon-pencil"></span> 編輯
              </button>
              
          </div>
      </div>  <!-- End panel-heading-->
      <div class="panel-body" v-if="loaded">
       
            <div class="row">
                 <div class="col-sm-3">
                      <label class="label-title">姓名</label>
                      <p>{{student.user.profile.fullname}}</p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">編號</label>
                      <p v-text="student.number"></p>                   
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">加入日期</label>
                      <p v-text="student.join_date"></p>                   
                 </div>
                  <div class="col-sm-3">
                      <label class="label-title">狀態</label>
                      <p v-html="statusLabel(student.active)"></p>
                  </div>
                  
            </div>   <!-- End row-->
            <div class="row">
                 
                 <div class="col-sm-3">
                      <label class="label-title">性別</label>
                       <p>{{ student.user.profile.gender|genderText }}</p>                  
                 </div>
                  <div class="col-sm-3">
                      <label class="label-title">身分證號</label>
                       <p>{{student.user.profile.SID}}</p>
                  </div>
                  <div class="col-sm-3">
                      <label class="label-title">生日</label>
                       <p>{{student.user.profile.dob}}</p>
                  </div>
                 
            </div>   <!-- End row-->
            <div class="row">
                  <div class="col-sm-3">
                      <label class="label-title">手機</label>
                      <p>{{ student.user.phone }}</p>                      
                 </div>
                 <div class="col-sm-6">
                      <label class="label-title">Email</label>
                      <p>{{ student.user.email }}</p>                    
                 </div>
                  
                
            </div>   <!-- End row-->
             <div class="row">
                 <div class="col-sm-9">
                      <label class="label-title">備註</label>
                      <p v-text="student.ps"></p>                   
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">最後更新</label>
                      <updated :entity="student"></updated>
                                     
                 </div>
            </div>   <!-- End row-->
           
      </div>  <!-- End panel-body-->
  
   </div>

  


</template>

<script>
   
    export default {
        name: 'ShowSudent',
        props: {
            id: {
              type: Number,
              default: 0
            },
            version: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            can_back:{
               type: Boolean,
               default: true
            },
        },
        data() {
            return {
               title:Helper.getIcon(Student.title())  + '  學員資料',
               loaded:false,
               student:null,
            }
        },
        watch:{
          'version' : 'init'
        },
        beforeMount(){
           this.init()
        },
        methods: {
           init(){
            
              this.loaded=false
              this.student=null
              if(this.id) this.fetchData()
              
           },
           fetchData() {
                let getData = Student.show(this.id)             
             
                getData.then(data => {
                   this.student= data.student
                   this.$emit('loaded',this.student)
                   this.loaded = true                        
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            courseNameText(){

            },
            statusLabel(avtive){
                let show_normal=true
                return Student.activeLabel(avtive, show_normal)
            },
            btnEditClicked(){   
              this.$emit('begin-edit') 
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },
            
          
        }, 
    }
</script>
