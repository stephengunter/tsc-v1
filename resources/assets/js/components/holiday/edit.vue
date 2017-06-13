<template>
<tr v-if="readOnly" >
     <td  v-text="holiday.name"></td> 
     <td  v-text="holiday.date"></td>
     <td> 
        <a v-if="holiday.updated_by"  href="#" @click.prevent="showUpdatedBy" >
          {{   holiday.updated_at|tpeTime  }}
        </a>
        <span v-else>{{   holiday.updated_at|tpeTime  }}</span>
     </td>           
     <td>
         <button v-if="can_edit" v-show="holiday.canEdit"  class="btn btn-primary btn-xs" 
            @click.prevent="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="holiday.canDelete"  class="btn btn-danger btn-xs"
            @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
</tr>
<tr v-else>
    <td v-if="loaded">
          <input type="text" name="holiday.name" @keydown="clearErrorMsg('holiday.name')" class="form-control" v-model="form.holiday.name">
          <small class="text-danger" v-if="form.errors.has('holiday.name')" v-text="form.errors.get('holiday.name')"></small>
    
    </td> 
     
    <td v-if="loaded">
        <div>
            <date-picker :option="datePickerOption" :date="begin_date" ></date-picker>
         
        </div>
        <input type="hidden" name="holiday.date" class="form-control" v-model="form.holiday.date"  >
        <small class="text-danger" v-if="form.errors.has('holiday.date')" v-text="form.errors.get('holiday.date')"></small>
    
    </td>
    <td v-if="loaded">
         <date-picker v-if="creating" :option="datePickerOption" :date="end_date" ></date-picker>
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
        name: 'EditHoliday',
        props: {
            holiday: {
               type: Object,
               default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            
        },
        
        data() {
            return {
                readOnly:true,

                begin_date: {
                    time: ''
                },
                end_date:{
                  time: ''
                },

                loaded:false,
                form:{},
                 
           
            }
        },
        computed:{
            creating(){
                let id=this.getId()
                if(id) return  false
                return true     
            }
        },
        watch:{
            begin_date: {
              handler: function () {
                  this.form.holiday.date=this.begin_date.time
                  this.clearErrorMsg('holiday.date')                 
              },
              deep: true
            },
            end_date: {
              handler: function () {
                  this.form.holiday.end_date=this.end_date.time
              },
              deep: true
            },
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.holiday) return Helper.tryParseInt(this.holiday.id)
                return 0
            },            
            init(){  
                if(this.holiday){
                   this.readOnly=true                  
                }else{
                    this.loaded=false
                    this.readOnly=false
                    this.fetchData()
                } 
                          
            },
            fetchData(){
                this.datePickerOption=Helper.datetimePickerOption()

                let getData=null
                let id=this.getId()
                if(id){
                    getData=Holiday.edit(id)
                }else{
                    getData=Holiday.create()
                }
                getData.then(data => {
                    let holiday = data.holiday
                    this.form=new Form({
                        holiday: holiday
                    }) 
                    
                    this.begin_date.time=holiday.date
                    this.end_date.time=''

                    
                    this.loaded=true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            
            beginEdit(){
                this.loaded=false
                this.readOnly=false
                this.fetchData()
            },
            cancelEdit(){
                let id=this.getId()
                if(id){
                   this.readOnly=true
                }else{
                    this.$emit('canceled')
                }
               
            },
            
            btnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.holiday.name
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
                    save=Holiday.update(this.form, id)
                }else{
                    save=Holiday.store(this.form)
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
            showUpdatedBy(){
               let updated_by=Helper.tryParseInt(this.holiday.updated_by)
               if(updated_by){
                  Bus.$emit('onShowEditor',updated_by)
               }
                
            },
            
            
            
        },

    }
</script>
