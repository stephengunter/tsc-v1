<template>
<tr v-if="readOnly" >
     
     <td  v-text="title.name"></td>   
     <td>
         <button class="btn btn-primary btn-xs" @click="onBeginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="title.canDelete"  class="btn btn-danger btn-xs" @click.prevent="onBtnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
</tr>
<tr v-else>
    
    <td v-if="loaded">
          <input type="text" name="title.name" @keydown="clearErrorMsg('title.name')" class="form-control" v-model="form.title.name">
          <small class="text-danger" v-if="form.errors.has('title.name')" v-text="form.errors.get('title.name')"></small>
    
    </td>
    <td v-if="loaded">
         
        <button @click.prevent="onSubmit"  class="btn btn-success btn-xs">
            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
        </button>  
         <button  class="btn btn-default btn-xs" @click.prevent="onCancelEdit">
             <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
        </button>
    </td> 
</tr>  
</template>


<script>
    export default {
        name: 'EditTitle',
        props: {
            title: {
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

                loaded:false,
                form:{},
                 
           
            }
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.title) return Helper.tryParseInt(this.title.id)
                return 0
            },            
            init(){  
                if(this.title){
                   this.readOnly=true                  
                }else{
                    this.loaded=false
                    this.readOnly=false
                    this.fetchData()
                } 
                          
            },
            fetchData(){
                let getData=null
                let id=this.getId()
                if(id){
                    getData=Title.edit(id)
                }else{
                    getData=Title.create()
                }
                getData.then(data => {
                    this.form=new Form({
                        title:data.title
                    }) 
                    this.loaded=true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            
            onBeginEdit(){
                this.loaded=false
                this.readOnly=false
                this.fetchData()
            },
            onCancelEdit(){
                this.$emit('canceled')
               
            },
            
            onBtnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.title.name
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
                    save=Title.update(this.form, id)
                }else{
                    save=Title.store(this.form)
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
