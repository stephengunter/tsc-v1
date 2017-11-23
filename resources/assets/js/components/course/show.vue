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
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="label-title">名稱</label>
                            <p>{{course.fullname}}</p>
                        </div>  
                        <div class="col-sm-4">
                            <label class="label-title">開課中心</label>  
                            <p> {{ course.center.name }}</p>                          
                        </div> 
                        <div class="col-sm-4">
                            <label class="label-title">學期</label>
                            <p v-text="course.term.name">                       
                            </p>
                        </div>    
                    </div> 
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="label-title">課程編號</label>
                            <p>{{course.number}}</p>
                        </div>  
                        <div class="col-sm-4">
                            <label class="label-title">課程分類</label>
                            <p  v-html="course.categoriesText"></p>
                        </div> 
                        <div class="col-sm-4">
                            <label class="label-title">教師</label>
                            <p v-html="course.teachersText"></p>
                        </div>    
                    </div>   
                    <div v-if="course.isGroup" class="row">
                        <div class="col-sm-4">
                            <label class="label-title">群組課程</label>
                            <p>
                                <span class="glyphicon glyphicon-ok-sign text-success"></span>
                            </p>
                        </div>  
                        <div v-if="course.hasParent" class="col-sm-4">
                            <label class="label-title">父課程</label>
                            <p v-html="course.parentCourse.name"></p>
                        </div> 
                        <div class="col-sm-4">
                            
                        </div>    
                    </div>  
                    <div v-if="course.isCredit" class="row">
                        <div class="col-sm-4">
                            <label class="label-title">學分數</label>
                            <p v-html="creditCountText()">  </p>
                        </div>  
                        <div  class="col-sm-4">
                            <label  class="label-title">學分單價</label>
                            <p> {{ course.credit_price | formatMoney }} </p>
                        </div> 
                        <div class="col-sm-4">
                           
                        </div>    
                    </div>  
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="label-title">課程簡介</label>
                            <p>{{  course.description }}</p>
                        </div>  
                          
                    </div> 
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="label-title">課程日期</label>
                            <p> {{course.begin_date}}
                                 ~
                                {{course.end_date}} 
                            </p>
                        </div>  
                        <div  class="col-sm-4">
                            <label  class="label-title">週數</label>
                            <p>{{  course.weeks }}</p>

                        </div> 
                        <div class="col-sm-4">
                            <label class="label-title">時數</label>
                            <p>{{  course.hours }}</p>
                        </div>    
                    </div> 
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="label-title">注意事項</label>
                            <p v-html="course.caution"></p>
                           
                        </div>  
                          
                    </div> 
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="label-title">狀態</label>
                            <p v-html="course.activeLabel">                       
                            </p>
                        </div>  
                        <div  class="col-sm-4">
                            <label class="label-title">審核</label>
                            <p v-if="course.hasReviewedBy" >
                                <a @click.prevent="showReviewedBy" href="#" v-html="$options.filters.reviewedLabel(course.reviewed)">                         
                                </a>
                                &nbsp;     
                                <button v-if="course.canReview" class="btn btn-primary btn-xs" @click.prevent="editReview" >
                                    <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                                </button>  
                            </p>
                            <p v-else> 
                                <span v-html="$options.filters.reviewedLabel(course.reviewed)"></span>     
                                &nbsp;        
                                <button class="btn btn-primary btn-xs" @click.prevent="editReview" >
                                    <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                                </button>             
                            </p>

                        </div> 
                        <div class="col-sm-4">
                            <label class="label-title">最後更新</label>
                            <updated :entity="course"></updated>
                        </div>    
                    </div>  <!-- End Row -->
                    <div v-if="course.status.ps" class="row">
                        <div class="col-sm-12">
                            <label class="label-title">備註</label>
                            <p v-text="course.status.ps">                       
                            </p>
                        </div> 
                    </div>  <!-- End Row -->    
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
        computed:{
            
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
            creditCountText(){
               return Course.creditCountText(this.course)
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
            showReviewedBy(){
                Bus.$emit('onShowEditor', parseInt(this.course.reviewed_by) , '審核者')
            },
            editReview(){
                this.$emit('edit-review')
            }

            

            
        }
    }
</script>