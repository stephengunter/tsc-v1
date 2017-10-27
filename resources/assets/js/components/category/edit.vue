<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">    
             <span class="panel-title">
                   <h4>
                       <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>

                       {{ title}}
                   </h4>
             </span>           
        </div>
        <div  v-if="loaded"  class="panel-body">  
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
                        <div v-show="!isServedCategory" class="form-group">                           
                            <label>代碼</label>
                            <input type="text" name="category.code" class="form-control" v-model="form.category.code"  >
                            <small class="text-danger" v-if="form.errors.has('category.code')" v-text="form.errors.get('category.code')"></small>
                        </div>
                    </div>
                    <div v-show="!isServedCategory" class="col-sm-3">
                         <div class="form-group">                           
                            <label>小圖樣式</label>
                            <input type="text" name="category.icon" class="form-control" v-model="form.category.icon"  >
                            <small class="text-danger" v-if="form.errors.has('category.icon')" v-text="form.errors.get('category.icon')"></small>
                        </div>
                    </div>
                    <div v-show="!isServedCategory" class="col-sm-3">
                        <div class="form-group">                           
                            <label>置頂</label>
                            <div>
                               <input type="hidden" v-model="form.category.public"  >
                               <toggle :items="publicOptions"   :default_val="form.category.public" @selected="setPublic"></toggle>
                            </div>
                        </div>  
                    </div>
                    
                </div>
                <div  v-show="false"  class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>狀態</label>
                            <div>
                               <input type="hidden" v-model="form.category.active"  >
                               <toggle :items="activeOptions"   :default_val="form.category.active" @selected="setActive"></toggle>
                            </div>
                        </div>
                    </div>
                </div>   
                <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group">                           
                            <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                             <button class="btn btn-default" @click.prevent="onCanceled">取消</button>  
                         </div>
                    </div>
                    
                </div><!-- </div> end row -->
                    
                
            </form>
           
        </div>
    </div>
    
</template>
<script>
    
    export default {
        name: 'EditCategory',
        props: {
            id: {
              type: Number,
              default: 0
            },
            course_id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                title:'',
                loaded:false,
                course:{},
                form: new Form({
                    category: {
                      
                    }
                }),

                isServedCategory:false,
                
                activeOptions: Helper.activeOptions(),

                publicOptions: Helper.boolOptions(),
            }
        },
        
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                this.form = new Form({
                    category: {}
                    
                })

                this.isServedCategory=false

                if(this.id){
                    this.title='  編輯課程分類'
                }else{
                    this.title='  新增課程分類'
                }
                this.fetchData() 
            },
            fetchData() {
                let getData=null
                if(this.id){
                    getData=Category.edit(this.id)
                }else{
                    getData=Category.create(this.course_id)
                }
                getData.then(data=>{
                    let category=data.category
                    this.form.category=category

                    if(category.public){
                        if(category.code=='latest' || category.code=='recommend'){
                            this.isServedCategory=true
                        }
                    }

                    this.loaded=true
                }).catch(error=>{
                   Helper.BusEmitError(error)  
                   this.loaded=false
                })  
            },
            setPublic(val){
                this.form.category.public=val
            },
            setActive(val){
                this.form.category.active=val
            },
            setStatus(val) {
                this.form.category.status = val;
            },
            
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                let store=null
                
                if(this.id){
                    store=Category.update(this.form , this.id)
                }else{
                    store=Category.store(this.form)
                }
               
                store.then(data => {
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