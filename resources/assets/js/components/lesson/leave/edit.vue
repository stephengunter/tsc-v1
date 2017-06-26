<template>
<tr v-if="readOnly" >
     <td  v-text="leave.user.profile.name"></td> 
     <td v-text="leave.typeName"></td>
     <td v-html="formattedLeaveTime"></td>
     <td v-text="leave.ps"></td>
     <td>
         <updated :entity="leave"></updated>
     </td>     
     <td>
         <button v-if="can_edit" v-show="leave.canEdit"  class="btn btn-primary btn-xs" 
            @click.prevent="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="leave.canDelete"  class="btn btn-danger btn-xs"
            @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
</tr>
<tr v-else>
    <td v-if="loaded">
       <drop-down  v-model="selectedUser" :options="userOptions" label="text"></drop-down>
       <small class="text-danger" v-if="form.errors.has('signup.user_id')" v-text="form.errors.get('signup.user_id')"></small>

    </td> 
    <td v-if="loaded">
        <select  v-model="form.leave.type_id"  class="form-control" >
             <option v-for="item in typeOptions" :value="item.value" v-text="item.text"></option>
        </select>
    </td> 
    <td v-if="loaded">
        <div>
            <time-picker :minute-interval="10" v-model="on" ></time-picker>
        </div>
        <small class="text-danger" v-if="form.errors.has('leave.on')" v-text="form.errors.get('leave.on')"></small>
    </td>
    <td v-if="loaded">
        <div>
            <time-picker :minute-interval="10" v-model="off"></time-picker>
        </div>
        <small class="text-danger" v-if="form.errors.has('leave.off')"  v-text="form.errors.get('leave.off')"></small>
                

    </td>
    <td>
        
    </td>
   
    <td v-if="loaded">
         
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
        name: 'EditLeave',
        props: {
            leave: {
               type: Object,
               default: null
            },
            lesson_id:{
               type:Number,
               default:0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            
        },
        watch:{
            on: function (val) {
                let time=Helper.getTimeSelected(val)

                if(time){
                    this.clearErrorMsg('leave.on')
                    this.form.leave.on=time
                }
            },
            off: function (val) {
                let time=Helper.getTimeSelected(val)

                if(time){
                    this.clearErrorMsg('leave.off')
                    this.form.leave.off=time
                }
            },
        },
        data() {
            return {
                readOnly:true,

                loaded:false,
                form:{},

                selectedUser:null,
                userOptions:[],
                begin:{},
                end:{},
                typeOptions: []
           
            }
        },
        computed:{

            creating(){
                let id=this.getId()
                if(id) return  false
                return true     
            }
        },
        
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.leave) return Helper.tryParseInt(this.leave.id)
                return 0
            },            
            init(){  
                if(this.leave){
                    this.readOnly=true                  
                }else{
                    this.loaded=false
                    this.readOnly=false
                    this.activeOptions= Helper.activeOptions()
                    this.fetchData()
                } 
                          
            },
            fetchData(){
                let getData=null
                let id=this.getId()
                if(id){
                    getData=Leave.edit(id)
                }else{
                    getData=Leave.create(this.lesson_id)
                }
                getData.then(data => {
                    let leave = data.leave
                    
                    this.form=new Form({
                        leave: leave
                    }) 

                    this.on=Helper.getTimeobj(leave.on)
                    this.off=Helper.getTimeobj(leave.off)
                    this.weekdayOptions = data.weekdayOptions
                   
                   
                    this.loaded=true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            formattedLeaveTime(){
                if(!this.leave) return ''
                 return  Leave.classTimeFullText(this.leave)
            },
            beginEdit(){
              
                this.loaded=false
                this.readOnly=false
                this.fetchData()

                this.$emit('editting', this.leave.id)
            },
            cancelEdit(){
                this.$emit('canceled')
               
            },
            setActive(val){
                this.form.leave.active=val
            },            
            btnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.leaveFulltext()
                }
                this.$emit('btn-delete-clicked' , values)
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                let save = null
                let id=this.getId()
                if(id){
                    save=Leave.update(this.form, id)
                }else{
                    save=Leave.store(this.form)
                }
             
                save.then(result => {
                   Helper.BusEmitOK()
                   this.readOnly=true;
                  
                   this.$emit('saved')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'存檔失敗')
                })
            },
            
            
        },

    }
</script>
