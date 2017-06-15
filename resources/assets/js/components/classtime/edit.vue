<template>
<tr v-if="readOnly" >
     <td  v-text="classtimeFulltext()"></td> 
     <td></td>
     <td></td>
     <td>
         <updated :entity="classtime"></updated>
     </td>     
     <td>
         <button v-if="can_edit" v-show="classtime.canEdit"  class="btn btn-primary btn-xs" 
            @click.prevent="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="classtime.canDelete"  class="btn btn-danger btn-xs"
            @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
</tr>
<tr v-else>
    <td v-if="loaded">
        <select  v-model="form.classtime.weekday_id"  name="classtime.weekday_id" class="form-control" >
            <option v-for="item in weekdayOptions" :value="item.value" v-text="item.text"></option>
        </select>
    </td> 
     
    <td v-if="loaded">
        <div>
            <time-picker :minute-interval="10" v-model="on" ></time-picker>
        </div>
        <small class="text-danger" v-if="form.errors.has('classtime.on')" v-text="form.errors.get('classtime.on')"></small>
    </td>
    <td v-if="loaded">
        <div>
            <time-picker :minute-interval="10" v-model="off"></time-picker>
        </div>
        <small class="text-danger" v-if="form.errors.has('classtime.off')"  v-text="form.errors.get('classtime.off')"></small>
                

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
        name: 'EditClasstime',
        props: {
            classtime: {
               type: Object,
               default: null
            },
            course_id:{
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
                    this.clearErrorMsg('classtime.on')
                    this.form.classtime.on=time
                }
            },
            off: function (val) {
                let time=Helper.getTimeSelected(val)

                if(time){
                    this.clearErrorMsg('classtime.off')
                    this.form.classtime.off=time
                }
            },
        },
        data() {
            return {
                readOnly:true,

                loaded:false,
                form:{},
                on:{},
                off:{},
                weekdayOptions: []
           
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
                if(this.classtime) return Helper.tryParseInt(this.classtime.id)
                return 0
            },            
            init(){  
                if(this.classtime){
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
                    getData=Classtime.edit(id)
                }else{
                    getData=Classtime.create(this.course_id)
                }
                getData.then(data => {
                    let classtime = data.classtime
                    
                    this.form=new Form({
                        classtime: classtime
                    }) 

                    this.on=Helper.getTimeobj(classtime.on)
                    this.off=Helper.getTimeobj(classtime.off)
                    this.weekdayOptions = data.weekdayOptions
                   
                   
                    this.loaded=true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            classtimeFulltext(){
                if(!this.classtime) return ''
                 return  Classtime.classTimeFullText(this.classtime)
            },
            beginEdit(){
              
                this.loaded=false
                this.readOnly=false
                this.fetchData()

                this.$emit('editting', this.classtime.id)
            },
            cancelEdit(){
                this.$emit('canceled')
               
            },
            setActive(val){
                this.form.classtime.active=val
            },            
            btnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.classtimeFulltext()
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
                    save=Classtime.update(this.form, id)
                }else{
                    save=Classtime.store(this.form)
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
