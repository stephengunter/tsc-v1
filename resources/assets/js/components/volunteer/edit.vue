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
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>姓名</label>
                            <div>
                                <input type="text" class="form-control" :value="form.volunteer.user.profile.fullname" disabled >
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>稱謂</label>
                            <select  v-model="form.volunteer.user.profile.title_id"  name="volunteer.user.profile.title_id" class="form-control" >
                                <option v-for="item in titleOptions" :value="item.value" v-text="item.text"></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>狀態</label>
                            <div>
                                <toggle :items="activeOptions"   :default_val="form.volunteer.active" @selected=setActive></toggle>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>加入日期</label>
                            <div>
                                <input type="hidden" name="volunteer.join_date" v-model="form.volunteer.join_date">
                                <date-picker  :date="join_date" :option="datePickerOption"></date-picker>
                            </div>
                            <small class="text-danger" v-if="form.errors.has('volunteer.join_date')" v-text="form.errors.get('volunteer.join_date')"></small>
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
        name: 'EditVolunteer',
        props: {
            id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                title:Helper.getIcon('Volunteers')  + '  編輯志工資料',
                loaded:false,
                form: new Form({
                    volunteer: {}
                }),

                join_date:{
                    time:''
                },
                datePickerOption: Helper.datetimePickerOption(),
                activeOptions:Helper.activeOptions(),
                titleOptions:[],

            }
        },
        watch:{
            join_date: {
              handler: function () {
                  this.form.volunteer.join_date=this.join_date.time
              },
              deep: true
            },
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                this.form = new Form({
                    volunteer: {
                        user:{
                            profile:{}
                        },
                    }
                }),
                this.fetchData() 
            },
            fetchData() {
                let getData=Volunteer.edit(this.id)  
                   getData.then(data=>{
                       this.form.volunteer=data.volunteer
                       this.form.user=data.user
                       this.join_date.time=data.volunteer.join_date

                       this.titleOptions=data.titleOptions
                       this.loaded=true 
                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })  
            },
            setActive(val){
                this.form.volunteer.active=val
            },
            
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                let update=Volunteer.update(this.form , this.id)
                
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