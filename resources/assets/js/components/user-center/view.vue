<template>
    <div>
        <div class="panel panel-default show-data">
            <div class="panel-heading">
                <span class="panel-title">
                    <h4>
                        <i class="fa fa-university" aria-hidden="true"></i>              
                        所屬中心
                    </h4>
                </span>
            
                <div>
                    <button v-if="can_edit" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                       <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                    </button>
                </div>
            
            </div>  
            <div v-if="loaded" class="panel-body">
                 <table class="table table-striped" style="width: 50%;">
                     <thead> 
                        <tr>                    
                            <th style="width:80%"></th> 
                            <th style="width:20%"></th>
                        </tr> 
                    </thead>
                    <tbody> 
                        <edit v-for="(center,index) in centers" :key="index"  :center="center" 
                           :UserCenters="UserCenters" :can_edit="can_edit"  
                            @btn-delete-clicked="onBtnDeleteClicked" >
                        </edit>
                        <edit   v-if="creating"  :UserCenters="UserCenters"
                          @saved="onCreated"  
                          @cancel="onCreateCanceled" > 
                        </edit> 
                    
                    </tbody> 
                </table>
            </div>     
        </div>   <!--  Panel  --> 
        <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
          @close="closeConfirm" @confirmed="deleteUserCenter">        
        </delete-confirm>     
    </div>
    

</template>

<script>
    import Edit from '../../components/user-center/edit.vue'
    export default {
        name: 'UserCenterView',
        components: {
             Edit
        },
        props: {
            user_id: {
              type: Number,
              default: 0
            },
            role: {
              type: String,
              default: ''
            },
            
            
        },
        
        beforeMount() {
           this.init()
        },
        
        data() {
            return {

                UserCenters:{},

                loaded:false,
                creating:false,
                
                centers:[],

                can_edit:false,

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
             
            }
        },
        methods: {
            init() {

                this.UserCenters=new UserCenters(this.user_id,this.role)
                this.loaded=false

                this.creating=false
                
                this.centers=[]
                this.can_edit=false
              
                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }
               
                this.fetchData()
            }, 
            
            fetchData() {
                let index=this.UserCenters.index()
               
                index.then(data => {
                    
                    this.can_edit=data.canEdit
                    

                    if(data.centers) {
                        let can_delete=this.can_edit
                        if(data.centers.length==1) can_delete=false
                        for (var i = data.centers.length - 1; i >= 0; i--) {
                            data.centers[i].canDelete=can_delete
                        }
                        this.centers=data.centers
                    }else{
                        this.centers=[]
                    }
                
                    
                    this.loaded = true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
           
            
            beginCreate(){
                this.centerOptions=[]
                this.creating=true
            },
            onCreateCanceled(){
                 this.creating=false
            },
            
            onBtnDeleteClicked(values){
                this.deleteConfirm.msg='確定要刪除與 ' + values.name + ' 的關聯嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true     
                
            },
            
           
            onCreated(){    
                this.init()
                this.$emit('created')
            },
            
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteUserCenter(){
                let center = this.deleteConfirm.id 
                let remove= this.UserCenters.delete(center)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.init()
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },
            
           
        },

    }
</script>