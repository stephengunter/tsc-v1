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
                <button  v-if="admin.canEdit" v-show="can_edit" @click="onBtnEditClicked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                </button>
                <button v-if="admin.canDelete" v-show="can_edit" @click="onBtnDeleteClicked" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
                </button>
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
      
            <div class="row">
                 
                 <div class="col-sm-3">
                      <label class="label-title">姓名</label>
                      <p v-text="admin.user.profile.fullname"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">角色</label>
                      <p>
                        <role-label :labelstyle="admin.roleModel.style" 
                        :labeltext="admin.roleModel.display_name">                        
                        </role-label>
                      </p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">狀態</label>
                      <p v-html="statusLabel(admin.active)">                       
                      </p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">最後更新</label>
                       <p>
                           <updated :entity="admin"></updated>
                       </p>
                 </div>
                
            </div>   <!-- End row-->
            <div class="row">
               <div class="col-sm-12">
                    <label class="label-title">所屬中心</label>
                    <p v-html="getCenterNames(admin.centers)"></p> 
                                    
                </div>
            </div>   <!-- End row-->
        </div><!-- End panel-body-->
    </div>
  


  


</template>

<script>
   
    export default {
        name: 'ShowAdmin',
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
               title:Helper.getIcon('Admins')  + '  系統管理員資料',
               loaded:false,
               admin:null,
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
              this.admin=null
              if(this.id) this.fetchData()
              
           },
           fetchData() {
                let getData = Admin.show(this.id)             
             
                getData.then(data => {
                   this.admin= data.admin
                   this.$emit('dataLoaded',this.admin)
                   this.loaded = true                        
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            getCenterNames(centers){
                return Admin.getCenterNames(centers)
            },
            statusLabel(active){
               return Admin.statusLabel(active)
            },
            onBtnEditClicked(){   
              this.$emit('begin-edit') 
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },
            onBtnDeleteClicked(){
                 let values={
                    name: this.admin.user.profile.fullname,
                    id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
            
          
        }, 
    }
</script>
