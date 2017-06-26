<template>
<div>
  <lesson v-show="loaded" :id="id" :can_edit="can_edit" :can_back="can_back"  
     @saved="lessonUpdated" @loaded="onDataLoaded" :version="current_version"
     @btn-back-clicked="onBtnBackClicked" @deleted="onLessonDeleted" > 

  </lesson>

  <div v-if="loaded" id="tabLesson" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                     <a @click="activeIndex=0" href="#students" data-toggle="tab">學員名單</a>
                </li>
                 <li>
                     <a @click="activeIndex=1" href="#leaves" data-toggle="tab">請假/缺席人員</a>
                </li>
            </ul>
           
               
         
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="students">
                    <students v-if="activeIndex==0" :lesson_id="id"></students>
                </div>
                <div class="tab-pane fade active in" id="leaves">
                    <!-- <students v-if="activeIndex==1" :lesson_id="id"></students> -->
                </div>               
            </div>
        </div>
  </div>



  


</div>
</template>

<script>
    import Lesson from '../../components/lesson/lesson.vue'
    import LessonStudents from '../../components/lesson/participant/list.vue'
    
    export default {
        name: 'LessonDetails',
        components: {
            Lesson,
            'students':LessonStudents
         
        },
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
               loaded:false,
               readonly:true,
               lesson:null,
               current_version:0,

               activeIndex:0,

               refundSettings:{
                  can_back:false
               },
               backTuitionSettings:{
                  hide_create:false
               }
            }
        },
        computed:{
           hasRefundRecord(){
              if(!this.lesson) return false
              if(!this.lesson.hasRefund) return false
                  return true
           }
        },
        beforeMount(){
           this.init()
        },
        methods: {
            init(){
              this.loaded=false
              this.readonly=true
              this.activeIndex=0
            },
            toBoolean(val){
               return val=='true'
            },
            onDataLoaded(lesson){
                this.loaded=true
                this.lesson=lesson
            },
            btnEditClicked(){    
              this.beginEdit()
            },
            beginEdit(){
               this.readonly=false
            },
            editCanceled(){
               this.readonly=true
            },
            lessonUpdated(){
               this.init()
            },
            onBtnBackClicked(){
                this.$emit('btn-back-clicked')
            },
            onLessonDeleted(){
               this.$emit('lesson-deleted')
            },
           
        }, 

    }
</script>