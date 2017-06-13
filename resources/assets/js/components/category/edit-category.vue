<template>
<div>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> {{ title }}</h4>  
             </span>           
        </div>
        <div class="panel-body" v-if="loaded">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>分類名稱</label>
                            <input type="text" name="category.name" class="form-control" v-model="form.category.name"  >
                            <small class="text-danger" v-if="form.errors.has('category.name')" v-text="form.errors.get('category.name')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                         <div class="form-group">                           
                            <label>小圖樣式</label>
                            <input type="text" name="category.icon" class="form-control" v-model="form.category.icon"  >
                            <small class="text-danger" v-if="form.errors.has('category.icon')" v-text="form.errors.get('category.icon')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>置頂</label>
                            <div>
                             <input type="hidden" v-model="form.category.public"  >
                            <toggle :items="publicOptions"   :defaultVal="form.category.public" @selected=setPublic></toggle>
                            </div>
                        </div>  
                    </div>
                    <div class="col-sm-3">
                         <div class="form-group">                           
                            <label>狀態</label>
                            <div>
                             <input type="hidden" v-model="form.category.active"  >
                            <toggle :items="activeOptions"   :defaultVal="form.category.active" @selected=setActive></toggle>
                            </div>
                        </div>
                    </div>
                </div>
             
                    
            
                <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group">                           
                            <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             <button class="btn btn-default" @click.prevent="endEdit">取消</button>  
                         </div>
                    </div>
                    
                </div><!-- </div> end row -->
                    
                
            </form>
        </div>
    </div>

     
    
     
</div>
</template>
<script>
    export default {
        name: 'EditCategory',
        props: ['id'],
        components: {
            'toggle': Toggle,
        },
        data() {
            return {
                title:'',
                loaded:false,
                form: new Form({
                    category: {}                   
                }),
                
                activeOptions: [{
                    text: '上架中',
                    value: '1'
                }, {
                    text: '已下架',
                    value: '0'
                }],

                publicOptions: [{
                    text: '是',
                    value: '1'
                }, {
                    text: '否',
                    value: '0'
                }],

            }
        },
        watch: {
            'id': 'fetchData'
        }, 
        beforeMount() {
            this.init()
        },
       
        methods: {
            init() {
               
                this.form = new Form({
                    category: {}
                })
                
                this.title=''
                this.fetchData()    
            }, 
            fetchData() {
                let id = this.id
                let url = '/api/categories/'

                if(!id){
                    url += 'create'
                    this.title='新增課程分類'
                }else{
                    
                    url += id + '/edit';
                    this.title='編輯課程分類'
                }  
                               
                axios.get(url)
                    .then(response => {
                        let category = response.data.category
                        this.form = new Form({
                            category: category,
                        })

                        this.loaded=true
                       
                    })
                    .catch(function(error) {
                        console.log(error)                            
                    })
            },
            setActive(val){
                this.form.category.active=val
            },
            setPublic(val){
                this.form.category.public=val
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
            submitForm(){
                let url = '/api/categories'
                let method='post'
                let id=this.id
                if(id){
                    method='put'
                    url += '/' + id 
                }
                
                this.form.submit(method,url)
                    .then(category => {
                       this.$emit('saved',category)
                      
                       Helper.BusEmitOK()     
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                           
                    })
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            
            endEdit(){
                this.$emit('endEditcategory')
            }




        },

    }
</script>