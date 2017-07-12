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
                <button v-if="course.canEdit" v-show="can_edit" @click="btnEditCilcked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                 </button>
                 <button v-if="course.canDelete" v-show="!hide_delete" @click="btnDeleteCilcked" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
                 </button>
               
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">
                    <photo :id="$options.filters.tryParseInt(course.photo_id)"></photo>
                </div>
                <div class="col-sm-3">
                  
                   
                    <label class="label-title">名稱</label>
                    <p>{{course.name}}</p>

                    <label class="label-title">課程編號</label>
                    <p>{{course.number}}</p>

                     <label class="label-title">起始日期</label>
                    <p>{{course.begin_date}}</p>

                   
                    
                </div>
                <div class="col-sm-3">
                    <label class="label-title">開課中心</label>
                    <p> {{ course.center.name }}</p>

                    <label class="label-title">課程分類</label>
                    <p v-html="course.categoriesText"></p>

                   

                    <label class="label-title">結束日期</label>
                     <p>{{  course.end_date }}</p>

                     

                </div>

                 <div class="col-sm-3">
                    <label class="label-title">學期</label>
                    <p v-text="course.term.name">                       
                    </p>
                    
                     <label class="label-title">教師</label>
                     <p v-html="course.teachersText"></p>

                   
                    <label class="label-title">學分數</label>
                    <p>
                         {{ course.credit_count }}
                    </p> 
                    
                </div>
            </div>  <!-- End row-->
            <div class="row">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-9">
                     <label class="label-title">課程簡介</label>
                     <p>{{  course.description }}</p>
                    
                </div>
                
            </div> 
            <div class="row">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-3">
                     <label class="label-title">週數</label>
                     <p>{{  course.weeks }}</p>

                
                  
                    
                </div>
                <div class="col-sm-3">
                     <label class="label-title">時數</label>
                     <p>{{  course.hours }}</p>
                   
                    
                </div>
               
                <div class="col-sm-3">
                    
                    
                </div>
            </div> 
            <div class="row">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-3">
                   <label class="label-title">上架狀態</label>
                    <p v-html="$options.filters.activeLabel(course.active)">                       
                    </p>
                   <!-- <label class="label-title">上課時間</label>
                   <p v-html="course.classTimesText">
                       
                   </p> -->
                </div>
                <div class="col-sm-3">
                   <label class="label-title">審核</label>
                    <p v-html="$options.filters.reviewedLabel(course.reviewed)">                       
                    </p>
                </div>
                <div class="col-sm-3">
                   <label class="label-title">最後更新</label>
                   <updated :entity="course"></updated>
               
                </div>
               
            </div> 

       
        </div>  <!-- End panel-body-->

    </div>

    
 

</div>
</template>
<script>
    export default {
        name: 'ShowCourse', 
        props: {
            id: {
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
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
             return {
                title:Helper.getIcon('Courses') + '  課程資料',
                loaded:false,
                course:null,

            }
        },
        watch: {
            'version':'init'
        },
        beforeMount() {
            this.init()
        },  
        methods: {    
            init(){
                this.loaded=false
                this.course=null
                if(this.id) this.fetchData()
               
            },
            fetchData() {
                let getData=Course.show(this.id)
               
                getData.then(data => {
                    this.course = new Course(data.course)
                    
                    this.loaded = true 
                    this.$emit('loaded',this.course)
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            },   
            
            btnEditCilcked(){
               this.$emit('begin-edit');
            },
            btnDeleteCilcked(){
                let values={
                  id:this.id,
                  name:this.course.name
                }
                this.$emit('btn-delete-clicked',values)
              
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },

            

            
        }
    }
</script>