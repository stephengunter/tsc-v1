<template>
<div v-show="loaded" class="panel panel-default show-data">
    <div class="panel-heading">
        <span class="panel-title">
           <h4>
              <i class="fa fa-calendar-check-o" aria-hidden="true"></i> 課堂紀錄表
           </h4>
        </span>
              
        <div>
             <button  @click="endShow" class="btn btn-default btn-sm" >
                 <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                 返回
             </button>
              <button class="btn btn-default btn-sm" @click.prevent="init">
                  <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
              </button>
             <button v-if="lesson.canEdit"  @click="btnEditClicked" class="btn btn-primary btn-sm" >
                <span class="glyphicon glyphicon-pencil"></span> 編輯
             </button>
             <button v-if="lesson.canDelete" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                <span class="glyphicon glyphicon-trash"></span> 刪除
             </button>
        </div>
    </div>  <!-- End panel-heading-->
    <div class="panel-body" v-if="loaded">
       
            <div class="row">
                 <div class="col-sm-3">
                      <label class="label-title">課程名稱</label>
                      <p v-text="courseNameText"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">日期</label>
                      <p v-text="dateFormatted"></p>                   
                 </div>
                  <div class="col-sm-3">
                      <label class="label-title">狀態</label>
                      <p v-html="statusLabel"></p>
                  </div>
                  <div class="col-sm-3">
                      <label class="label-title">順序</label>
                       <p v-text="lesson.order"></p>    
                  </div>
            </div>   <!-- End row-->
            <div class="row">
                 <div class="col-sm-3">
                      <label class="label-title">上課時間</label>
                      <p v-text="classTimeText"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">教室</label>
                      <p v-if="lesson.classroom" v-text="lesson.classroom.name"></p>
                      <p v-else></p>                   
                 </div>
                  <div class="col-sm-3">
                      <label class="label-title">授課老師</label>
                      <p v-html="teacherNames"></p>
                  </div>
                  <div class="col-sm-3">
                      <label class="label-title">教育志工</label>
                       <p v-html="volunteerNames"></p>    
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
                      <p v-if="!lesson.updated_by"> {{   lesson.updated_at|tpeTime  }}</p>
                      <p v-else>
                        <a  href="#" @click.prevent="showUpdatedBy" >
                            {{   lesson.updated_at|tpeTime  }}
                        </a>
                        
                      </p>                      
                 </div>
                 
                
            </div>   <!-- End row-->
            
           
       
   </div><!-- End panel-body-->
</div>

</template>

<script>
    import LessonScripts from '../../scripts/lesson.js'
    export default {
        name: 'ShowLesson',
        props: ['id',  'canEdit'],
        data() {
            return {
              lessonScripts:{},
              loaded:false,
              lesson:{},
            }
        },
        computed: {
            courseNameText() {
                return this.lessonScripts.courseNameText()
            },
            statusLabel() { 
                return this.lessonScripts.statusLabel()
            },
            dateFormatted(){
                 return this.lessonScripts.dateFormatted()
            },
            classTimeText(){
                return this.lessonScripts.classTimeText()
            },
            teacherNames(){
                return this.lessonScripts.teacherNames()
            },
            volunteerNames(){
                return this.lessonScripts.volunteerNames()
            }
           
        },
        beforeMount(){
           this.init()
        },
        methods: {
          init(){
              this.loaded=false

              this.lessonScripts={}
              this.lesson={}
              
              this.fetchData()
          },
          fetchData() {
                let url = '/api/lessons/' + this.id                
                axios.get(url)
                    .then(response => {
                       let lesson= response.data.lesson
                       this.lesson=lesson
                       this.lessonScripts=new LessonScripts(lesson)
                       this.loaded = true                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            btnEditClicked(){
              
              this.$emit('beginEdit')
            },
            endShow(){
                this.$emit('endShow')
            },
            btnDeleteClicked(){
               this.$emit('beginDelete')
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.lesson.updated_by)
            },
        }, 

    }
</script>