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
                <button v-if="user.canEdit" v-show="can_edit" @click="btnEditCilcked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                 </button>
                 <button v-if="user.canDelete" v-show="!hide_delete" @click="btnDeleteCilcked" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
                 </button>
               
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">
                    <photo :id="$options.filters.tryParseInt(user.profile.photo_id)"></photo>
                </div>
                <div class="col-sm-3">
                  
                   
                    <label class="label-title">Email</label>
                    <p>{{user.email}}</p>
                       <label class="label-title">真實姓名</label>
                    <p>{{user.profile.fullname}}</p>
                    <label class="label-title">身分證號</label>
                      <p>{{user.profile.SID}}</p>
                    <label class="label-title">建檔日期</label>
                    <p>{{ user.created_at | tpeTime  }}</p>
                    
                </div>
                <div class="col-sm-3">
                    
                      <label class="label-title">手機</label>
                    <p>{{user.phone}}</p>
                     <label class="label-title">性別</label>
                      <p>{{ user.profile.gender|genderText }}</p>
                    <label class="label-title">生日</label>
                      <p>{{user.profile.dob}}</p>
                       
                    
                    <label class="label-title">最後更新</label>
                     <updated :entity="user"></updated>
                </div>
                <div class="col-sm-3">
                    
                      <label class="label-title">角色</label>
                        <p> 
                      
                            <button type="button" class="btn btn-default btn-xs" @click="onAddRoleCilcked">
                              <span class="glyphicon glyphicon-plus"></span>
                            </button>
                            <role-label v-for="role in user.roles" :labelstyle="role.style" 
                            :labeltext="role.display_name">
                            </role-label>
                            
                        </p>
                     <label class="label-title">稱謂</label>
                      <p v-text="user.titleText"></p>
                    
                </div>
           </div>  <!-- End row-->

       
        </div>  <!-- End panel-body-->

    </div>

    
 

</div>
</template>
<script>

    import RoleLabel from '../../components/RoleLabel.vue'
    

    export default {
        name: 'ShowUser',        
        components: {
            RoleLabel,
        },
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
                title:Helper.getIcon('Users') ,
                loaded:false,
                user:null,

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
                this.user=null
                if(this.id) this.fetchData()
               
            },
            fetchData() {
                let getUser=User.show(this.id)
               
                getUser.then(data => {
                    this.user = new User(data.user)
                   
                    this.title += '  ' + this.user.name
                    this.loaded = true 
                    this.$emit('user-loaded',this.user)
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            },  
            onAddRoleCilcked(){
               this.$emit('add-role');
            },
            btnEditCilcked(){
               this.$emit('begin-edit');
            },
            btnDeleteCilcked(){
                let values={
                  id:this.id,
                  name:this.user.name
                }
                this.$emit('btn-delete-clicked',values)
              
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },

            

            
        }
    }
</script>