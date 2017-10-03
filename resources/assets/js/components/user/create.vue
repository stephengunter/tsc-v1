<template> 
 <div>   
    <div class="panel panel-default">
        
        <div class="panel-heading">
           
             <span class="panel-title">
                   <h4 v-html="title"></h4>  
             </span> 
             <a href="#" @click.prevent="introSettings.show=true">
                  <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
                  說明
             </a>
        </div>
        <div v-if="loaded" class="panel-body">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            
                            <label>Email</label>
                            <input type="text" name="user.email" class="form-control" v-model="form.user.email" >
                            <small class="text-danger" v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')"></small>
                        </div>
                     
                        <div class="form-group">
                            <label>密碼</label>
                            <input type="text" name="user.password" class="form-control" v-model="form.user.password" readonly>
                           
                        </div>
                    </div>
                    <div class="col-sm-4">
                           <div class="form-group">
                            <label>使用者名稱</label>
                            <input type="text" name="user.name" class="form-control" v-model="form.user.name" >
                             <small class="text-danger" v-if="form.errors.has('user.name')" v-text="form.errors.get('user.name')"></small>
                        </div>
                        <div class="form-group">
                            <label>手機</label>
                            <input type="text" name="user.phone" class="form-control" v-model="form.user.phone" >
                            <small class="text-danger" v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')"></small>
                        </div>
                    </div>
                     <div class="col-sm-4">
                         <div class="form-group">
                            <label>真實姓名</label>
                            <input type="text" name="user.profile.fullname" class="form-control" v-model="form.user.profile.fullname" >
                             <small class="text-danger" v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')"></small>
                        </div>
                        <div class="form-group">
                            <label>性別</label>
                           
                            <div>
                             <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected=setGender></toggle>
                            </div>
                        </div>
                    </div>
                </div>
                
                      <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                      <button type="button" class="btn btn-default" @click.prevent="onCencel">取消</button>
              
                
            </form>
        </div>
    </div>

    <modal :showbtn="false" :width="introSettings.width" :show.sync="introSettings.show"  @closed="introSettings.show=false" 
        effect="fade">
          <div slot="modal-header" class="modal-header">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="introSettings.show=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
           <h3>新增使用者說明</h3>
          </div>
        <div slot="modal-body" class="modal-body show-data">
          <ul>
            <li>Email或手機至少要填一個</li>
            <li>預設密碼是六個0 &nbsp; 使用者日後可自行變更</li>
          </ul>
            
      
        </div>
    </modal> 
  </div>   
</template>
<script>

    export default {
        name: 'CreateUser',
        data() {
            return {
                title:Helper.getIcon('Users')  + '  新增使用者',
                loaded:false,
                showAlert: false,
                form: new Form({
                    user: {
                        profile: {

                        }
                    },
                }),
                genderOptions: User.genderOptions(),
               introSettings:
                {
                    show:false,
                    width:600,
                    

                }

            }
        },
        beforeMount() {
            this.init()
        },
        watch: {

        },
        methods: {
            init(){
                this.fetchData() 
            },
            fetchData() {
                let create= User.create()
                create.then(data => {
                    let user = data.user
                    this.form = new Form({
                        user: user,

                    });

                    this.loaded=true
                })
                .catch(error => {
                    Helper.BusEmitError(error)
                })
            },
            onSubmit() {
                 this.submitForm()
            },
            submitForm() {
                let store=User.store(this.form)
                
                store.then(user => {
                    Helper.BusEmitOK('存檔成功')
                    this.$emit('saved',user)
                }).catch(error => {
                    Helper.BusEmitError(error,'存檔失敗')                        
                })
            },


            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            setGender(val) {
                this.form.user.profile.gender = val;
            },

            onCencel() {
                this.$emit('cenceled')
            }


        },

    }
</script>