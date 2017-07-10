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
              <button  v-if="notice.canEdit" v-show="can_edit" @click="btnEditClicked" class="btn btn-primary btn-sm" >
                  <span class="glyphicon glyphicon-pencil"></span> 編輯
              </button>
              <button v-if="notice.canDelete" v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                  <span class="glyphicon glyphicon-trash"></span> 刪除
              </button>
          </div>
      </div>  <!-- End panel-heading-->
      <div class="panel-body" v-if="loaded">
       
            <div class="row">
                <div class="col-sm-12">
                   <label class="label-title">標題</label>
                   <p v-text="notice.title"></p>
                </div>  
                 
            </div>   <!-- End row-->
            <div class="row">
                <div class="col-sm-12">
                   <label class="label-title">內容</label>
                   <p v-html="notice.content"></p>
                </div>  
            </div>   <!-- End row-->
            <div class="row">
                <div class="col-sm-3">
                      <label class="label-title">狀態</label>
                      <p v-html="$options.filters.activeLabel(notice.active)">  
                      </p>
                </div>
                <div class="col-sm-3">
                      <label class="label-title">發佈到網站</label>
                      <p>  {{  notice.public |  boolText }} </p>
                                     
                </div>
                <div class="col-sm-3">
                      <label class="label-title">Email給學員</label>
                      <p>  {{  notice.emails |  boolText }} </p>
                                     
                </div>
            </div>   <!-- End row-->
            <div v-show="notice.emails" class="row">
                <div class="col-sm-12">
                   <label class="label-title">Email課程名單</label>
                   <p>
                     <span v-for="course in notice.courseNames">
                       {{ course }}   &nbsp;
                     </span>
                   </p>
                </div>  
                 
            </div>   <!-- End row-->
            <div class="row">
                <div class="col-sm-3">
                      <label class="label-title">建檔日期</label>
                      <p>  {{  notice.created_at }} </p>
                                     
                </div>
                <div class="col-sm-3">
                      <label class="label-title">最後更新</label>
                      <updated :entity="notice"></updated>
                                     
                </div>
            </div>   <!-- End row-->
      </div>  <!-- End panel-body-->
  
   </div>

  


</template>

<script>
   
    export default {
        name: 'ShowNotice',
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
               title:Helper.getIcon(Notice.title())  + '  公告訊息',
               loaded:false,
               notice:null,
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
              this.notice=null
              if(this.id) this.fetchData()
              
           },
           fetchData() {
                let getData = Notice.show(this.id)             
             
                getData.then(data => {
                   let notice= data.notice
                   if(notice.courses){
                      notice.emails=true
                   }else{
                      notice.emails=false
                   }

                   this.notice=notice
                   this.$emit('loaded',notice)
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
                    name: '確定要刪除此公告訊息嗎？',
                    id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
          
        }, 
    }
</script>
