<template>
<tr>
     
     <td v-if="isReadOnly"  v-text="center.name"></td> 
     
     <td v-if="isReadOnly">
         
        <button v-if="can_edit" v-show="center.canDelete"  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
           <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
        </button>
     </td> 

     <td v-if="!isReadOnly">
          <select  v-model="form.center"   style="width:auto;" class="form-control selectWidth">
               <option v-for="option in centerOptions" :value="option.value" v-text="option.text"></option>
          </select>
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
        name: 'EditUserCenter',
        props: {
            center: {
               type: Object,
               default: null
            },
            UserCenters: {
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
                centerOptions:[],

                form:{}
                 
           
            }
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.center) return this.center.id
                return 0
            },
            init(){  
                if(this.center){
                   this.isReadOnly=true
                  
                }else{
                    this.form=new Form({
                        id:this.UserCenters.id,
                        role:this.UserCenters.role,
                        center:0 
                    }) 
                    this.isReadOnly=false
                    this.fetchData()
                } 
                          
            },
            fetchData(){
                let create=this.UserCenters.create()
                create.then(data => {
                   this.centerOptions=data.options   
                   this.form.center = this.centerOptions[0].value
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            showDelete(){
                let id=this.getId()
                return (this.isReadOnly && id>0 );
            },
            
            cancelEdit(){
               
                if(this.center){
                     this.isReadOnly=true
                }else{
                    this.$emit('cancel')
                }
                
            },
            btnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.center.name
                }
                this.$emit('btn-delete-clicked' , values)
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                let store = this.UserCenters.store(this.form)
                store.then(result => {
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
