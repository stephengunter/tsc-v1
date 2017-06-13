<template>
<div v-show="loaded">
    
    <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        <div class="row">
            
            <div class="col-sm-1">

                <div class="form-group">  
                    <label>順序</label>
                    <select  v-model="form.schedule.order"  name="schedule.order" class="form-control" >
                         <option v-for="item in orderOptions" :value="item.value" v-text="item.text"></option>
                     </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">  
                    <label>課目標題</label>
                    <input type="text" name="schedule.title" class="form-control" v-model="form.schedule.title">
                    <small class="text-danger" v-if="form.errors.has('schedule.title')" v-text="form.errors.get('schedule.title')"></small>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">  
                    <label>內容</label>
                     <textarea rows="5" cols="50" class="form-control" name="schedule.content"  v-model="form.schedule.content">
                     </textarea>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">  
                    <label>材料</label>
                    <input type="text" name="schedule.materials" class="form-control" v-model="form.schedule.materials">
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
        name: 'CreateSchedule',
        props: ['course_id'],
        components: {
           
        },
        data() {
            return {
                loaded:false,
                name,
                form: new Form({
                    schedule: {
                    }
                }),
                orderOptions:{},
            }
        },
        
        beforeMount() {
           this.init();
        },
        methods: {
            init(){  
                this.loaded=false
                this.orderOptions=Helper.numberOptions(1,60)            
                
                this.fetchData()               
            },
            fetchData() {
                let url = '/api/schedules/create?course=' + this.course_id
              
                axios.get(url)
                     .then(reaponse => {
                        let schedule=reaponse.data.schedule 

                        this.form = new Form({
                            schedule: schedule
                        })
                        
                        this.loaded=true
                     })
                     .catch( error=> {
                        console.log(error)                            
                    })
               
            },
            cancelEdit(){
               this.$emit('cancelCreate')
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
                let url = '/api/schedules'
                let method = 'post'
                this.form.submit(method,url)
                    .then(schedule => {
                         Helper.BusEmitOK()
                       this.$emit('saved', schedule)
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
