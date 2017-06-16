<template>
    <div>
        <show-lesson v-if="isReadOnly" :id="id"  @loaded="onLessonLoaded"
          @endShow="endShow" @beginEdit="beginEdit" @beginDelete="beginDelete">
        </show-lesson>

        <edit-lesson v-if="!isReadOnly"  :id="id"
            @saved="lessonUpdated"   @canceled="endEdit" >                 
        </edit-lesson>

        <div v-if="loaded" id="tabCourse" class="panel with-nav-tabs panel-default">
            <div class="panel-heading">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a @click="activeIndex=1" href="#participant" data-toggle="tab">出席狀況</a>
                        </li>
                        <!-- <li class="">
                            <a @click="activeIndex=2" href="#classtime" data-toggle="tab">上課時間</a>
                        </li> -->
                       
                        
                    </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="participant">
                        <!-- <signup v-if="activeIndex==1" :id="id" :canEdit="true"></signup>   -->
                    </div>
                    <!-- <div class="tab-pane fade" id="classtime">
                         <classtime v-if="activeIndex==2" :course_id="id"></classtime>
                    </div>
                    <div class="tab-pane fade" id="schedule">
                        <schedule v-if="activeIndex==3" :course_id="id"></schedule>
                    </div> 
                     <div class="tab-pane fade" id="lesson">
                        <lesson v-if="activeIndex==4" :course_id="id"></lesson>
                    </div>   -->                      
                </div>
            </div>
        </div>   <!-- end tabCourse -->
       
        <modal :showbtn="true"  :show.sync="showConfirm" @ok="deletelesson"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
            <div slot="modal-header" class="modal-header modal-header-danger">
               
                <button id="close-button" type="button" class="close" data-dismiss="modal" @click="closeConfirm">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
                 <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
            </div>
            <div slot="modal-body" class="modal-body">
                <h3 v-text="confirmMsg"> </h3>
            </div>
        </modal>

    </div>
</template>


<script>

    import ShowLesson from '../../components/lesson/show-lesson.vue'
    import EditLesson from '../../components/lesson/edit-lesson.vue' 

    export default {
        props: ['id'],
        components:{
           'show-lesson':ShowLesson,
           'edit-lesson':EditLesson,
            'modal': Modal,
        },
        name:'LessonView',
        data()  {
            return {                
                isReadOnly:true,
                showConfirm:false,
                confirmMsg:'',
                lesson:{},
                loaded:false,
                activeIndex:1
            }
        },
        beforeMount(){
           this.init()
        },
        watch: {
            'id': 'init'
        },
        methods:{
            init(){
                this.loaded=false
                this.isReadOnly=true
                this.showConfirm=false
                this.confirmMsg=''
                this.activeIndex=1
                this.lesson={}
               
            },
            onLessonLoaded(lesson){
                 this.lesson=lesson
                 this.loaded=true
            },
            endShow(){
                this.$emit('endShow')
            },
            beginEdit(){
                this.isReadOnly=false
            },
            endEdit(){
                this.isReadOnly=true
            },
            lessonUpdated(lesson){
               this.init()
            },
            closeConfirm(){
                this.showConfirm=false
            },
            beginDelete(){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要刪除此課程進度嗎？'               
                    this.showConfirm=true
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
                
            },
            deletelesson(){
                let url = '/api/lessons/' + this.id 
                let form=new Form()
                form.delete(url)
                .then(result => {
                    this.init()
                    let msg={
                          title:'刪除成功',
                          status: 200
                        }
                    Bus.$emit('okmsg',msg); 

                    this.$emit('deleted')
                   
                    this.closeConfirm();
                })
                .catch(error => {
                    let msgtitle = '刪除失敗'                        
                    
                    Bus.$emit('errors', {
                            title: msgtitle,
                            status: error.status
                    })

                    this.closeConfirm();
                       
                })
            },
        }

    }
</script>