<template>
<tr v-if="isReadOnly" >
     <td v-text="identity.name"></td>
     <td v-text="isMemberText()"></td>
     <td v-text="identity.ps"></td> 
             
     <td>
         <button class="btn btn-primary btn-xs" @click="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="identity.canDelete"  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
</tr>
<tr v-else>
    <td v-if="loaded">
          <input type="text" name="identity.name" @keydown="clearErrorMsg('identity.name')" class="form-control" v-model="form.identity.name">
          <small class="text-danger" v-if="form.errors.has('identity.name')" v-text="form.errors.get('identity.name')"></small>
    
    </td> 
    <td v-if="loaded">
        <div>  
           <input type="hidden" v-model="form.identity.member"  >
           <toggle :items="boolOptions"   :default_val="form.identity.member" @selected=setMember></toggle>
        </div>
    </td> 
    <td v-if="loaded">
         <textarea rows="5" cols="50" name="identity.ps" class="form-control" v-model="form.identity.ps"> </textarea>
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
        name: 'EditIdentity',
        props: {
            identity: {
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
                isReadOnly:true,

                boolOptions:[],

                loaded:false,
                form:{},
                 
           
            }
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.identity) return Helper.tryParseInt(this.identity.id)
                return 0
            },            
            init(){  
                if(this.identity){
                   this.isReadOnly=true                  
                }else{
                    this.loaded=false
                    this.isReadOnly=false
                    this.fetchData()
                } 
                          
            },
            fetchData(){
                let getData=null
                let id=this.getId()
                if(id){
                    getData=Identity.edit(id)
                }else{
                    getData=Identity.create()
                }
                getData.then(data => {
                    this.form=new Form({
                        identity:data.identity
                    }) 
                    this.loadOptions(id)
                   
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            isMemberText(){
                if(parseInt(this.identity.member)>0) return '是'
                    return ''
            },
            loadOptions(id){
                
                this.boolOptions=Helper.boolOptions()
                this.loaded=true
            },
           
            beginEdit(){
                this.loaded=false
                this.isReadOnly=false
                this.fetchData()
            },
            cancelEdit(){
                this.$emit('canceled')
            },
            setMember(val) {
                this.form.identity.member = val;
            },
            btnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.identity.name
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
                    save=Identity.update(this.form, id)
                }else{
                    save=Identity.store(this.form)
                }
             
                save.then(result => {
                   Helper.BusEmitOK()
                   this.isReadOnly=true;
                  
                   this.$emit('saved')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'存檔失敗')
                })
            },
           
            
            
            
        },

    }
</script>
