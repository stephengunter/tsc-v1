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
              <button  v-if="lesson.canEdit" v-show="can_edit" @click="btnEditClicked" class="btn btn-primary btn-sm" >
                  <span class="glyphicon glyphicon-pencil"></span> 編輯
              </button>
              <button v-if="lesson.canDelete" v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                  <span class="glyphicon glyphicon-trash"></span> 刪除
              </button>
          </div>
      </div>  <!-- End panel-heading-->
      <div class="panel-body" v-if="loaded">
       
            <div class="row">
                 <div class="col-sm-3">
                      <label class="label-title">課程名稱</label>
                      <p v-text="lesson.courseNameText"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">日期</label>
                      <p v-text="lesson.dateFormatted"></p>                   
                 </div>
                  <div class="col-sm-3">
                      <label class="label-title">狀態</label>
                      <p v-html="lesson.statusLabel"></p>
                  </div>
                  <div class="col-sm-3">
                      <label class="label-title">順序</label>
                       <p v-text="lesson.order"></p>    
                  </div>
            </div>   <!-- End row-->
            <div class="row">
                 <div class="col-sm-3">
                      <label class="label-title">上課時間</label>
                      <p v-text="lesson.classTimeText"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">教室</label>
                      <p v-if="lesson.classroom" v-text="lesson.classroom.name"></p>
                      <p v-else></p>                   
                 </div>
                  <div class="col-sm-3">
                      <label class="label-title">授課老師</label>
                      <p v-html="lesson.teacherNames"></p>
                  </div>
                  <div class="col-sm-3">
                      <label class="label-title">教育志工</label>
                       <p v-html="lesson.volunteerNames"></p>    
                  </div>
            </div>   <!-- End row-->
            <div class="row">
                 <div class="col-sm-6">
                      <label class="label-title">課目標題</label>
                      <p v-text="lesson.title"></p>                      
                 </div>
                 <div class="col-sm-6">
                      <label class="label-title">內容重點</label>
                      <p v-text="lesson.content"></p>                   
                 </div>
                
            </div>   <!-- End row-->
             <div class="row">
                 <div class="col-sm-6">
                      <label class="label-title">教材</label>
                      <p v-text="lesson.materials"></p>                      
                 </div>
                 <div class="col-sm-6">
                      <label class="label-title">備註</label>
                      <p v-text="lesson.ps"></p>                   
                 </div>
                
            </div>   <!-- End row-->
            <div class="row">
                 <div class="col-sm-3">
                      <label class="label-title">最後更新</label>
                      <updated :entity="lesson"></updated>
                      <!-- <p v-if="!lesson.updated_by"> {{   lesson.updated_at|tpeTime  }}</p>
                      <p v-else>
                        <a  href="#" @click.prevent="showUpdatedBy" >
                            {{   lesson.updated_at|tpeTime  }}
                        </a>
                        
                      </p>   -->                    
                 </div>
            </div>   <!-- End row-->
    </div>
  


  


</template>

<script>
   
    export default {
        name: 'ShowSignup',
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
               title:Helper.getIcon(Lesson.title())  + '  課堂紀錄表',
               loaded:false,
               lesson:null,
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
              this.lesson=null
              if(this.id) this.fetchData()
              
           },
           fetchData() {
                let getData = Lesson.show(this.id)             
             
                getData.then(data => {
                   let lesson= data.lesson
                   this.lesson=new Lesson(lesson)
                   this.$emit('loaded',lesson)
                   this.loaded = true                        
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            btnEditClicked(){   
              this.$emit('begin-edit') 
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },
            btnDeleteClicked(){
                 let values={
                    name: '確定要刪除此課程進度嗎？',
                    id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
          
        }, 
    }
</script>
