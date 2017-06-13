<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="fa fa-key" aria-hidden="true"></i> 新增管理員
            </h4>  
        </div>
        <div class="panel-body" v-if="loaded">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            
                            <label>姓名</label>
                            <input type="text" name="profile.fullname" class="form-control" v-model="form.profile.fullname" >
                            <small class="text-danger" v-if="form.errors.has('profile.fullname')" v-text="form.errors.get('profile.fullname')"></small>
                        </div>
                        
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>性別</label>
                            <div>
                            <input type="hidden" v-model="form.profile.gender"  >
                            <toggle :items="genderOptions"   :defaultVal="form.profile.gender" @selected=setGender></toggle>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>角色</label>
                            <select  v-model="form.admin.role"  name="admin.role" class="form-control" >
                                <option v-for="item in roleOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                    </div>
                   
              </div>  <!-- end row-->
               <div class="row">
                    <div class="col-sm-4">
                       <div class="form-group">
                        <label>手機號碼</label>
                        <input type="text" name="user.phone" class="form-control" v-model="form.user.phone">
                        <small class="text-danger" v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')"></small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">  
                            <label>所屬中心</label>
                             <drop-down :value.sync="centers" multiple  :options="centerOptions" label="text"></drop-down>
                            <small class="text-danger" v-if="form.errors.has('centers')" v-text="form.errors.get('centers')"></small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                       
                    </div>
                    
              </div>  <!-- end row-->
                
              <button class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        
              <button class="btn btn-default" @click.prevent="backToIndex">取消</button>
              
                
            </form>
        </div>
    </div>
</template>
<script>
    
    export default {
        name: 'CreateAdmin',
        components: {
            'toggle': Toggle,
            'drop-down':DropDown,
            'date-picker' : MyDatepicker
        },
        data() {
            return {
                 id:0,
                form: {},
                gender: 1,

                centers:[],

                genderOptions:[],
                roleOptions:[],
                centerOptions:[],

                loaded:false,
                
            }
        },
        
        beforeMount() {
            this.init()
        },
        watch: {
            $route:function(){
                this.init()
            },
            centers: function (val) {
               if(val.length){
                 this.clearErrorMsg('centers')
               }
            },
        },
        methods: {
            init(){
                
                this.id=this.$route.params.id
                this.loaded=false
                this.genderOptions= Helper.genderOptions()
                this.datePickerOption=Helper.datetimePickerOption()
                this.form= new Form({
                    user:{},
                    profile:{},
                    admin:{},
                })
                this.fetchData()
            },
            
            fetchData() {
                let url = '/api/admins/create?user=' + this.id 
                axios.get(url)
                    .then(response => {
                        let user = response.data.user
                        let admin= response.data.admin
                        this.form = new Form({
                            user:{
                                id:user.id,
                                phone:user.phone                                
                            },
                            profile:{
                                fullname:user.profile.fullname,
                                gender:user.profile.gender,
                            },
                            admin:admin,
                            centers:[]
                        })

                        this.roleOptions=response.data.roleOptions
                       
                        this.centerOptions=response.data.centerOptions

                        this.loaded=true
                    })
                    .catch(error => {
                       console.log(error)
                    })
            },
            setGender(val) {
                this.form.profile.gender = val;
            },
            onSubmit() {
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.submitForm()
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
            },
            submitForm() {
                let method = 'post'
                let url = '/api/admins' 
                
                this.form.centers=this.centers
               
                this.form.submit(method, url)
                    .then(user => {
                       Helper.BusEmitOK()
                       this.backToIndex()
                    }).catch(error => {
                        Helper.BusEmitError(error,'存檔失敗')                        
                    })
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },

            backToIndex() {
                this.$router.push('/admins')
            },
        },

    }
</script>