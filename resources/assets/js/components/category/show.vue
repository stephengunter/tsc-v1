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
              <button  v-if="category.canEdit" v-show="can_edit" @click="btnEditClicked" class="btn btn-primary btn-sm" >
                  <span class="glyphicon glyphicon-pencil"></span> 編輯
              </button>
              <button v-if="category.canDelete" v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                  <span class="glyphicon glyphicon-trash"></span> 刪除
              </button>
          </div>
      </div>  <!-- End panel-heading--> 
      <div v-if="loaded" class="panel-body">
       
            <div class="row">
                 <div class="col-sm-3">
                      <label class="label-title">分類名稱</label>
                      <p v-text="category.name"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">小圖</label>
                     
                      <p v-html="$options.filters.showIcon(category.icon)">                       
                      </p>                     
                 </div>
                  <div class="col-sm-3">
                      <label class="label-title">狀態</label>
                      <p v-html="$options.filters.activeLabel(category.active)">                       
                      </p>
                  </div>
                  <div class="col-sm-3">
                      <label class="label-title">最後更新</label>
                      <updated :entity="category"></updated>
                  </div>
            </div>   <!-- End row-->
            
           
       
      </div><!-- End panel-body-->


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
               title:Helper.getIcon(Category.title())  + '  課程分類',
               loaded:false,
               category:null,
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
              this.category=null
              if(this.id) this.fetchData()
              
           },
           fetchData() {
                let getData = Category.show(this.id)             
             
                getData.then(data => {
                   let category= data.category
                   this.category=new Category(category)
                   this.$emit('loaded',category)
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
