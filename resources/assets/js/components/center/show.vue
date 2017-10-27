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
                <button v-if="center.canEdit" v-show="can_edit" @click="btnEditCilcked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                 </button>
                 <button v-if="center.canDelete" v-show="!hide_delete" @click="btnDeleteCilcked" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
                 </button>
               
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">
                    <photo :id="$options.filters.tryParseInt(center.photo_id)"></photo>
                </div>
                <div class="col-sm-3">
                  
                   
                    <label class="label-title">名稱</label>
                    <p>{{center.name}}</p>
                   
                    <label class="label-title">建檔日期</label>
                      <p>{{  center.created_at | tpeTime }}</p>
                    
                </div>
                <div class="col-sm-3">
                    <label class="label-title">代碼</label>
                    <p> {{ center.code }} </p>

                    <label class="label-title">最後更新</label>
                     <p v-if="!center.updated_by"> 
                          {{   center.updated_at|tpeTime  }}
                     </p>
                     <p v-else>
                         <a  href="#" @click.prevent="showUpdatedBy" >
                            {{   center.updated_at|tpeTime  }}
                         </a>
                     </p>  
                </div>
                <div class="col-sm-3">
                  
                   
                     <label class="label-title">狀態</label>
                     <p v-html="$options.filters.activeLabel(center.active)">                       
                     </p>
                   
                  
                    
                </div>
           </div>  <!-- End row-->

       
        </div>  <!-- End panel-body-->

    </div>

    
 

</div>
</template>
<script>
    export default {
        name: 'ShowCenter', 
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
                title:Helper.getIcon('Centers') + '  開課中心',
                loaded:false,
                center:null,

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
                this.center=null
                if(this.id) this.fetchData()
               
            },
            fetchData() {
                let getData=Center.show(this.id)
               
                getData.then(data => {
                    this.center = data.center
                    
                    this.loaded = true 
                    this.$emit('loaded',this.center)
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            },   
            showUpdatedBy(){
               let updated_by=Helper.tryParseInt(this.center.updated_by)
               if(updated_by){
                  Bus.$emit('onShowEditor',updated_by)
               }
                
            },
            btnEditCilcked(){
               this.$emit('begin-edit');
            },
            btnDeleteCilcked(){
                let values={
                  id:this.id,
                  name:this.center.name
                }
                this.$emit('btn-delete-clicked',values)
              
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },

            

            
        }
    }
</script>