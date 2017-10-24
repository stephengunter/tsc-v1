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
                <button  v-if="teacher.canEdit" v-show="can_edit" @click="btnEditClicked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                </button>
                <button v-if="teacher.canDelete" v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
                </button>
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
      
            <div class="row">
                 
                 <div class="col-sm-4">
                      <label class="label-title">姓名</label>
                      <p v-text="teacher.name"></p>                      
                 </div>
                 <div class="col-sm-4">
                      <label class="label-title">專長</label>
                      <p v-text="teacher.specialty"></p>                      
                 </div>
                 <div class="col-sm-4">
                      <label class="label-title">最高學歷</label>
                      <p v-text="teacher.education"></p>                      
                 </div>
                
            </div>   <!-- End row-->
            <div v-if="isGroup" class="row">
                 <div class="col-sm-4">
                      <label class="label-title">群組</label>
                      <p>
                        <span v-html="$options.filters.okSign(true)"></span>
                      </p>
                      
                                          
                 </div>
             </div>   <!-- End row-->
            <div v-else class="row">
                 <div class="col-sm-4">
                      <label class="label-title">現職</label>

                      <p v-text="teacher.job"></p>                     
                 </div>
                  <div class="col-sm-4">
                      <label class="label-title">職稱</label>
                      <p v-text="teacher.jobtitle"></p> 
                  </div>
                  <div class="col-sm-4">
                      <label class="label-title">教師證書號</label>
                      <p v-text="teacher.certificate"></p>
                  </div>
                 
                  
             </div>   <!-- End row-->
             <div class="row">
                 <div class="col-sm-4">
                      <label class="label-title">學經歷</label>
                      <p v-html="teacher.experiences"></p>                     
                 </div>
                  <div class="col-sm-4">
                      <label class="label-title">個人簡介</label>
                      <p v-text="teacher.description"></p> 
                  </div>
                  <div class="col-sm-4">
                      <label class="label-title">資料審核</label>
                      
                      <p v-if="hasReviewedBy" >
                          <a @click.prevent="showReviewedBy" href="#" v-html="$options.filters.reviewedLabel(teacher.reviewed)">                         
                          </a>
                          &nbsp;     
                          <button v-if="teacher.canReview" class="btn btn-primary btn-xs" @click.prevent="editReview" >
                              <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                          </button>  
                      </p>
                      <p v-else > 
                          <span v-html="$options.filters.reviewedLabel(teacher.reviewed)"></span>     
                          &nbsp;     
                          <button class="btn btn-primary btn-xs" @click.prevent="editReview" >
                              <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                          </button>             
                      </p>
                      
                  </div>
             </div>   <!-- End row-->
             <div class="row">
                 <div class="col-sm-4">
                      <label class="label-title">建檔日期</label>
                     <p>{{ teacher.created_at | tpeTime  }}</p>           
                 </div>
                  <div class="col-sm-4">
                      <label class="label-title">最後更新</label>
                      <p v-if="!teacher.updated_by"> {{   teacher.updated_at|tpeTime  }}</p>
                      <p v-else>
                        <a  href="#" @click.prevent="showUpdatedBy" >
                            {{   teacher.updated_at|tpeTime  }}
                        </a>
                        
                      </p> 
                  </div>
                  <div v-show="false" class="col-sm-4">
                      <label class="label-title">狀態</label>
                      <p v-html="$options.filters.activeLabel(teacher.active)">                       
                      </p>
                  </div>
             </div>   <!-- End row-->
       
        </div><!-- End panel-body-->
    </div>
  

   


</template>

<script>
   
    export default {
        name: 'ShowTeacher',
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
               title:Helper.getIcon('Teachers')  + '  教師資料',
               loaded:false,
               teacher:null,

               
             }
        },
        computed:{
            isGroup(){
                if(!this.teacher) return false
                  return Helper.isTrue(this.teacher.group)
            },
            hasReviewedBy(){
                if(!this.teacher) return false
                if(!this.teacher.reviewed_by) return false
                return parseInt(this.teacher.reviewed_by)
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
              this.signup=null
              if(this.id) this.fetchData()
              
           },
           fetchData() {
                let getData = Teacher.show(this.id)             
             
                getData.then(data => {
                   this.teacher= data.teacher
                   this.$emit('dataLoaded',this.teacher)
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
                    name: this.teacher.name,
                    id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',parseInt(this.teacher.updated_by))
            },
            showReviewedBy(){
                Bus.$emit('onShowEditor', parseInt(this.teacher.reviewed_by) , '審核者')
            },
            editReview(){
                this.$emit('edit-review')
            }
          
        }, 
    }
</script>
