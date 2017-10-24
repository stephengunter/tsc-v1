<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">    
             <span class="panel-title">
                   <h4 v-html="title"></h4>
             </span>           
        </div>
        <div class="panel-body">
            <form v-if="loaded" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>姓名</label>
                            <div>
                                <input type="text" class="form-control" :value="user.profile.fullname" disabled >
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>角色</label>
                            <div>
                                <toggle :items="roleOptions"   :default_val="form.admin.role" @selected=setRole></toggle>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>狀態</label>
                            <div>
                                <toggle :items="activeOptions"   :default_val="form.admin.active" @selected=setActive></toggle>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">                           
                           <button type="submit"  class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                           <button type="button" class="btn btn-default" @click.prevent="onCanceled">取消</button>  
                        </div>
                    </div>
      
                </div><!-- row    -->
            </form>
        </div>
    </div>
    
</template>
<script>
    
    export default {
        name: 'EditAdmin',
        props: {
            id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                title:Helper.getIcon('Admins')  + '  編輯系統管理員資料',
                loaded:false,
                user:{},
                form: new Form({
                    admin: {}
                }),
                activeOptions:Admin.statusOptions(),
                roleOptions:[],

            }
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                this.form = new Form({
                    admin: {
                        
                    }
                }),
                this.fetchData() 
            },
            fetchData() {
                let getData=Admin.edit(this.id)  
                   getData.then(data=>{
                       this.form.admin=data.admin

                       this.user=data.user
                       this.roleOptions=data.roleOptions

                       this.loaded=true 
                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })  
            },
            setActive(val){
                this.form.admin.active=val
            },
            setRole(val){
                this.form.admin.role=val
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                let update=Admin.update(this.form , this.id)
                
                update.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
         
           
            onCanceled(){
                this.$emit('canceled')
            }




        },

    }
</script>