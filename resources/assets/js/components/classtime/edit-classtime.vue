<template>
<div v-show="loaded">
    <div v-if="isReadOnly" id="readClasstime" class="row">
        <div class="col-sm-9">
            <p style="font-size:21px">{{ name }}
            &nbsp;&nbsp;
                <button v-if="canEdit" class="btn btn-primary btn-xs" @click="beginEdit">
                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                </button>  
                 <button v-if="canEdit"  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </button>
            </p> 
                               
        </div>
    </div> <!--  end  readClasstime-->
    <form v-if="!isReadOnly" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        <div class="row">
            
            <div class="col-sm-3">

                <div class="form-group">  
                    <label>課程日</label>
                    <select  v-model="form.classTime.weekday_id"  name="classTime.weekday_id" class="form-control" >
                        <option v-for="item in weekdayOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">  
                    <label>上課時間</label>
                    <div>
                        <time-picker :minute-interval="10" v-model="on" ></time-picker>
                    </div>
                    <small class="text-danger" v-if="form.errors.has('classTime.on')" v-text="form.errors.get('classTime.on')"></small>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">  
                    <label>下課時間</label>
                    <div>
                        <time-picker :minute-interval="10" v-model="off"></time-picker>
                    </div>
                    <small class="text-danger" v-if="form.errors.has('classTime.off')"  v-text="form.errors.get('classTime.off')"></small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <button type="submit" class="btn btn-success" >確認送出</button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button class="btn btn-default" @click.prevent="cancelEdit"  >取消</button>
            </div>

        </div> 
    </form>
</div>     
</template>


<script>
    export default {
        name: 'EditClassTime',
        props: ['classTime','course_id','canEdit'],
        components: {
            'time-picker':TimePicker,
        },
        data() {
            return {
                loaded:false,
                isReadOnly:true,
                
                name,
                form: new Form({
                    classTime: {
                    }
                }),
                weekdayOptions:{},

                on:{},
                off:{},
            }
        },
        watch:{
            on: function (val) {
                let time=Helper.getTimeSelected(val)

                if(time){
                    this.clearErrorMsg('classTime.on')
                }
            },
            off: function (val) {
                let time=Helper.getTimeSelected(val)

                if(time){
                    this.clearErrorMsg('classTime.off')
                }
            },
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.classTime)   return this.classTime.id
                return 0
            },
            init(){  
                this.loaded=false
                this.isReadOnly=true 
                let id = this.getId()     
                if (id < 1) {
                    this.isReadOnly=false 
                    this.fetchData()      
                }else{
                    this.name=this.getFullText(this.classTime) 
                    this.loaded=true 
                }     
                             
            },
            fetchData() {

                let url = this.getFetchDataUrl()
              
                axios.get(url)
                     .then(response => {
                        let classTime=response.data.classTime 
                        this.on=Helper.getTimeobj(classTime.on)
                        this.off=Helper.getTimeobj(classTime.off)
                        this.weekdayOptions = response.data.weekdayOptions

                        this.form = new Form({
                            classTime: classTime
                        })
                        
                        this.loaded=true
                     })
                     .catch( error=> {
                        console.log(error)                            
                    })
               
            },
            getFetchDataUrl(){
                let url = '/api/classtimes/';
               
                let id=this.getId()

                if (id > 0) {
                    url += id + '/edit';                   
                } else {
                   url +='create?course=' + this.course_id;
                }

                return url
            },
            getFullText(classTime){
                return Helper.classTimeFullText(classTime)                
            },
            showDelete(){
                return (this.isReadOnly && this.id>0 );
            },
            beginEdit(){
               
                this.isReadOnly=false
                this.fetchData()
               
            },
            cancelEdit(){
                let id=this.getId()
                if(id){
                     this.isReadOnly=true
                }else{
                    this.$emit('endEdit')
                }
                
            },
            btnDeleteClicked(){
                
                let values={
                    id:this.getId(),
                    name:this.name
                }
                this.$emit('btnDeleteClicked' , values)
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
                let url = '/api/classtimes'
                let id=this.getId()
                let method = 'post'
                if (id) {
                    method = 'put'
                    url += '/' + id 
                }

                this.form.classTime.on=Helper.getTimeSelected(this.on)
                 this.form.classTime.off=Helper.getTimeSelected(this.off)

                this.form.submit(method,url)
                    .then(classTime => {
                        Helper.BusEmitOK()

                       this.form = new Form({
                            classTime: classTime
                        })

                       if(id){
                          this.name=this.getFullText(classTime) 
                          this.isReadOnly=true;
                       }
                      
                       this.$emit('saved', classTime)
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                    })
            },
            
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            
            
        },

    }
</script>
