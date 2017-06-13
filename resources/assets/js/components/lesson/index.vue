<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-calendar-check-o" aria-hidden="true"></i> 課堂紀錄表</h4>
            </span>
            <div>
                <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="viewMore=!viewMore">
                <span v-if="viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                  <span v-if="!viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
                </button>
                
            </div>
            <div>
                <button class="btn btn-default btn-sm" @click.prevent="init">
                  <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                </button>
                <button v-show="canEdit" class="btn btn-warning btn-sm" @click.prevent="showInitialize=true">
                  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span> 初始化
                </button>

                <button v-show="canEdit" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
               
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 99%;">
             <thead> 
                <tr> 
                    <th style="width:5%"></th> 
                    <th style="width:3%">#</th> 
                    <th style="width:12%">日期</th>
                    <th style="width:8%">狀態</th> 
                    <th style="width:11%">時間</th> 
                    <th style="width:11%">地點</th>


                    <!-- 50 -->

                   
                    <th v-if="!viewMore"  style="width:15%">課目標題</th>  
                    <th v-if="!viewMore" style="width:15%">內容重點</th>
                    <th v-if="!viewMore"  style="width:10%">教材</th> 
                    <th v-if="!viewMore"  style="width:10%">備註</th> 

                    <!-- 50 -->
                    <th v-if="viewMore" style="width:19%">授課老師</th> 
                    <th v-if="viewMore" style="width:19%">教育志工</th>
                    <th v-if="viewMore" style="width:12%">學生應到/實到</th> 
                    

                </tr>
            </thead>
            <tbody> 
               <lesson-row v-for="lesson in lessonList" :lesson="lesson" 
               :viewMore="viewMore" @beginShow="onBeginShow">
               </lesson-row>
              
            </tbody> 
            </table>

            

          

        </div><!-- End panel-body-->

    </div>
   

    <modal :showbtn="false" :width="800" :show.sync="showInitialize"  @closed="showInitialize=false" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="showInitialize=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           
          </div>
        <div slot="modal-body" class="modal-body">
           <initialize v-if="showInitialize" :course_id="course_id" @saved="init"
           @initializeCanceled="showInitialize=false"></initialize>
        </div>
    </modal>

    

</div>

</template>

<script>
     import InitializeLessons from '../../components/lesson/initialize.vue'
     
     import LessonRow from '../../components/lesson/lesson-row.vue'
    export default {
        name: 'LessonIndex',
        components: {
            'initialize':InitializeLessons,
            'lesson-row':LessonRow,
            
             'modal': Modal,
        },
        props: ['course_id','canEdit'],
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.lessonList.length) return true
                return false    
            }
        },
        data() {
            return {
                loaded:false,
                viewMore:false,
                creating:false,
                lessonList:[],
               
                showInitialize:false,
              
                orderOptions:{},
             
            }
        },
        methods: {
            init() {
                this.loaded=false
                this.creating=false
               
                this.showInitialize=false
                this.lessonList=[]
             
               
                this.orderOptions={}
                
                this.fetchData()         
            }, 
            fetchData() {
                let url = '/api/lessons?course=' + this.course_id                
                axios.get(url)
                    .then(response => {
                       
                       this.lessonList=response.data.lessonList
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            onBeginShow(id){
               this.$emit('onBeginShow',id)
            },
            beginCreate(){
                 this.$emit('beginCreate')
            },
            endCreate(){
                 this.creating=false
            },
            cancelEdit(){
               
               this.$emit('endEditlesson')
            },
            cancelCreate(){
               this.creating=false
               
            },
           
            lessonUpdated(lesson){ 
                  this.init()
            },
            
           
        },

    }
</script>