<template>
    <div class="panel panel-default">
        
        <div class="panel-heading">           
             <span class="panel-title">
                  <h4>
                    <i class="fa fa-info-circle" aria-hidden="true"></i>  編輯課程報名資訊
                  </h4> 
             </span>           
        </div>
        <div class="panel-body">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="row">
                    
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>人數上限</label>
                            <input type="text" name="signupinfo.limit" class="form-control" v-model="form.signupinfo.limit">
                            <small class="text-danger" v-if="form.errors.has('signupinfo.limit')" v-text="form.errors.get('signupinfo.limit')"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>最低人數</label>
                            <input type="text" name="signupinfo.min" class="form-control" v-model="form.signupinfo.min">
                            <small class="text-danger" v-if="form.errors.has('signupinfo.min')" v-text="form.errors.get('signupinfo.min')"></small>
                        </div>
                        
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>網路報名</label>
                            <div>
                                <input type="hidden" v-model="form.signupinfo.net_signup"  >
                                <toggle :items="discountOptions"   :default_val="form.signupinfo.net_signup" @selected="setNetSignup"></toggle>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>折扣</label>
                            <div>
                                <input type="hidden" v-model="form.signupinfo.discount"  >
                                <toggle :items="discountOptions"   :default_val="form.signupinfo.discount" @selected="setDiscount"></toggle>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>學費</label>
                            <input type="text" name="signupinfo.tuition" class="form-control" v-model="form.signupinfo.tuition">
                            <small class="text-danger" v-if="form.errors.has('signupinfo.tuition')" v-text="form.errors.get('signupinfo.tuition')"></small>
                        </div>
                    </div>
                    
                    <div class="col-sm-3">
                        <div class="form-group">                           
                            <label>教材費</label>
                            <input type="text" name="signupinfo.cost" class="form-control" v-model="form.signupinfo.cost">
                            <small class="text-danger" v-if="form.errors.has('signupinfo.cost')" v-text="form.errors.get('signupinfo.cost')"></small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">                           
                            <label>教材</label>
                            <textarea rows="4" cols="50" class="form-control" name="signupinfo.materials"  v-model="form.signupinfo.materials">
                            </textarea>
                            
                           <small class="text-danger" v-if="form.errors.has('signupinfo.materials')" v-text="form.errors.get('signupinfo.materials')"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                  
                    <div class="col-sm-12">
                        <div class="form-group">                           
                            <label>注意事項</label>
                                <textarea rows="4" cols="50" class="form-control" name="signupinfo.caution"  v-model="form.signupinfo.caution">
                                </textarea>
                            <small class="text-danger" v-if="form.errors.has('signupinfo.caution')" v-text="form.errors.get('signupinfo.caution')"></small>
                        </div>
                    </div>
                   
                </div>
                <div class="row">
                    <div class="col-sm-3">
                         <button type="submit" class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         <button class="btn btn-default" @click.prevent="cancel">取消</button>
                    </div>
                    

                </div>
                    
                
            </form>
            
        </div>
    </div>


   


</template>
<script>
    export default {
        name: 'EditsignupinfoInfo',
        props: {
            id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                loaded:false,
               
                form: new Form({
                   signupinfo:{}
                }),
               
                discountOptions:[{
                    text: '可以',
                    value: 1
                }, {
                    text: '不行',
                    value: 0
                }]
           

            }
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                
                this.fetchData() 
             
               
            },
            fetchData() {
                let id=this.id
                let getData=SignupInfo.edit(id)
                
                getData.then(data=>{
                    let signupinfo = new SignupInfo(data.signupinfo)
                    if(!signupinfo.cost){
                       signupinfo.cost=0
                    }
                    signupinfo.materials = Helper.replaceAll(signupinfo.materials, '<br>', '\n')
                    signupinfo.caution = Helper.replaceAll(signupinfo.caution, '<br>', '\n')
                    this.form = new Form({
                            signupinfo: signupinfo,
                        })

                 

                    this.loaded=true


                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })           
                
                
            },
            
            setNetSignup(val){
                this.form.signupinfo.net_signup = val
            },
            setDiscount(val){
                this.form.signupinfo.discount = val
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                if(this.form.signupinfo.materials){
                   let materials=Helper.replaceAll( this.form.signupinfo.materials, '\n','<br>')
                   let caution=Helper.replaceAll( this.form.signupinfo.caution, '\n','<br>')
                   this.form.signupinfo.materials=materials
                   this.form.signupinfo.caution=caution
                }
                this.submitForm()
            },
            submitForm() {
                let id=this.id
                let update=SignupInfo.update(this.form, id)
                
                update.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
            cancel(){
                this.$emit('canceled')
            },
            




        },

    }
</script>