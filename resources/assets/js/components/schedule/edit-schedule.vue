<template>
<tr>

     <th v-if="isReadOnly"  scope="row" v-text="schedule.order"></th> 
     <td v-if="isReadOnly"  v-text="schedule.title"></td> 
     <td v-if="isReadOnly"  v-text="schedule.content"></td>
     <td v-if="isReadOnly"  v-text="schedule.materials"></td> 
     <td v-if="isReadOnly">
         <button class="btn btn-primary btn-xs" @click="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
    
     <td v-if="!isReadOnly">
          <select  v-model="form.schedule.order"  class="form-control" >
               <option v-for="item in orderOptions" :value="item.value" v-text="item.text"></option>
           </select>
     </td> 
     <td v-if="!isReadOnly">
         <input type="text" name="schedule.title" @keydown="clearErrorMsg('schedule.title')" class="form-control" v-model="form.schedule.title">
          <small class="text-danger" v-if="form.errors.has('schedule.title')" v-text="form.errors.get('schedule.title')"></small>
     </td> 
     <td v-if="!isReadOnly">
          <textarea rows="5" cols="50" class="form-control" name="schedule.content"  v-model="form.schedule.content">
          </textarea>
     </td>
     <td v-if="!isReadOnly">
         <textarea rows="5" cols="50" name="schedule.materials" class="form-control" v-model="form.schedule.materials"> </textarea>
     </td>
     <td v-if="!isReadOnly">
         
        <button @click.prevent="onSubmit"  class="btn btn-success btn-xs">
            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
        </button>  
         <button  class="btn btn-default btn-xs" @click.prevent="cancelEdit">
             <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
        </button>
     </td> 
</tr>  
</template>


<script>
    export default {
        name: 'EditSchedule',
        props: ['schedule'],
        components: {
           
        },
        data() {
            return {
                isReadOnly:true,
                form: new Form({
                    schedule: {
                    }
                }),
                orderOptions:[],
                
            }
        },
        methods: {
            getId(){
                if(this.schedule) return this.schedule.id
                return 0
            },
            init(){  
                this.loaded=false
                this.isReadOnly=true  
                this.form= new Form({
                    schedule: {
                    }
                })          
            },
            showDelete(){
                let id=this.getId()
                return (this.isReadOnly && id>0 );
            },
            beginEdit(){
                if(!this.orderOptions.length){
                    this.orderOptions=Helper.numberOptions(1,60)       
                }
                this.form= new Form({
                    schedule: this.schedule
                }) 
                this.isReadOnly=false
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
                    name:this.schedule.title
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
                let method = 'put'
                let id=this.getId()
                let url = '/api/schedules/' + id   
                this.form.submit(method,url)
                    .then(schedule => {
                        Helper.BusEmitOK()

                       this.form = new Form({
                            schedule: schedule
                        })

                       this.isReadOnly=true;
                      
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
