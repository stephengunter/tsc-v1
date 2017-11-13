<template>
<tr v-if="readOnly" >
     <td  v-text="classroom.center.name"></td> 
     <td  v-text="classroom.name"></td>
     <td  v-html="$options.filters.activeLabel(classroom.active)" ></td> 
     <td  v-text="classroom.ps"></td>
     <td> 
        <a v-if="classroom.updated_by"  href="#" @click.prevent="showUpdatedBy" >
          {{   classroom.updated_at|tpeTime  }}
        </a>
        <span v-else>{{   classroom.updated_at|tpeTime  }}</span>
     </td>           
     <td>
         <button v-if="can_edit" v-show="classroom.canEdit"  class="btn btn-primary btn-xs" 
            @click.prevent="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="classroom.canDelete"  class="btn btn-danger btn-xs"
            @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
</tr>
<tr v-else>
    <td >
         
    </td> 
     
    <td v-if="loaded">
       <input type="text" name="classroom.name" @keydown="clearErrorMsg('classroom.name')" class="form-control" v-model="form.classroom.name">
       <small class="text-danger" v-if="form.errors.has('classroom.name')" v-text="form.errors.get('classroom.name')"></small>
    
    </td>
    <td v-if="loaded">
         <input type="hidden" v-model="form.classroom.active"  >
         <toggle :items="activeOptions"   :default_val="form.classroom.active" @selected="setActive"></toggle>
          
    </td>
    <td v-if="loaded">
         <textarea rows="5" cols="50" name="classroom.ps" class="form-control" v-model="form.classroom.ps"> </textarea>  
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
        name: 'EditClassroom',
        props: {
            classroom: {
               type: Object,
               default: null
            },
            center_id:{
               type:Number,
               default:0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            
        },
        
        data() {
            return {
                readOnly:true,

                loaded:false,
                form:{},
               
                activeOptions: []
           
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
                if(this.classroom) return Helper.tryParseInt(this.classroom.id)
                return 0
            },            
            init(){  
                if(this.classroom){
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
                    getData=Classroom.edit(id)
                }else{
                    getData=Classroom.create()
                }
                getData.then(data => {
                    let classroom = data.classroom
                    if(!id){
                       classroom.center_id=this.center_id
                    }
                    this.form=new Form({
                        classroom: classroom
                    }) 

                   
                   
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
            setActive(val){
                this.form.classroom.active=val
            },            
            btnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.classroom.name
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
                    save=Classroom.update(this.form, id)
                }else{
                    save=Classroom.store(this.form)
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
               let updated_by=Helper.tryParseInt(this.classroom.updated_by)
               if(updated_by){
                  Bus.$emit('onShowEditor',updated_by)
               }
                
            },
            
            
            
        },

    }
</script>
