<template>
<tr v-if="isReadOnly" >
     <th  scope="row" v-text="term.year"></th> 
     <td  v-text="term.order"></td> 
     <td  v-text="term.name"></td>
     <td  v-text="term.number"></td> 
     <td  v-html="$options.filters.activeLabel(term.active)" ></td>
     <td> 
        <a v-if="term.updated_by"  href="#" @click.prevent="showUpdatedBy" >
          {{   term.updated_at|tpeTime  }}
        </a>
        <span v-else>{{   term.updated_at|tpeTime  }}</span>
        

     </td>           
     <td>
         <button class="btn btn-primary btn-xs" @click="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="term.canDelete"  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
</tr>
<tr v-else>
    <td v-if="loaded">
          <select @change="clearErrorMsg('term.number')"   v-model="form.term.year"  class="form-control" >
               <option v-for="item in yearOptions" :value="item.value" v-text="item.text"></option>
          </select>
    </td> 
    <td v-if="loaded">
          <select @change="clearErrorMsg('term.number')"  v-model="form.term.order"  class="form-control" >
               <option v-for="item in orderOptions" :value="item.value" v-text="item.text"></option>
           </select>
    </td> 
    <td v-if="loaded">
          <input type="text" name="term.name" @keydown="clearErrorMsg('term.name')" class="form-control" v-model="form.term.name">
          <small class="text-danger" v-if="form.errors.has('term.name')" v-text="form.errors.get('term.name')"></small>
    
    </td>
    <td v-if="loaded">
      <small class="text-danger" v-if="form.errors.has('term.number')" v-text="form.errors.get('term.number')"></small>
   
    </td>
    <td v-if="loaded">
          <input type="hidden" v-model="form.term.active"  >
           <toggle :items="activeOptions"   :default_val="form.term.active" @selected=setActive></toggle>
                            
    </td>
    <td v-if="loaded">
         
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
        name: 'EditTerm',
        props: {
            term: {
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

                orderOptions:[],
                yearOptions:[],
                activeOptions:[],

                loaded:false,
                form:{},
                 
           
            }
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.term) return Helper.tryParseInt(this.term.id)
                return 0
            },            
            init(){  
                if(this.term){
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
                    getData=Term.edit(id)
                }else{
                    getData=Term.create()
                }
                getData.then(data => {
                    this.form=new Form({
                        term:data.term
                    }) 
                    this.loadOptions(id)
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            loadOptions(id){
                this.activeOptions=CommonService.activeOptions()
                this.yearOptions=Term.yearOptions()
                this.orderOptions=Term.orderOptions()  

                if(!id){
                    this.form.term.year=this.yearOptions[0].value
                    this.form.term.order=this.orderOptions[0].value
                }
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
            setActive(val){
                this.form.term.active=val
            },
            btnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.term.name
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
                    save=Term.update(this.form, id)
                }else{
                    save=Term.store(this.form)
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
            showUpdatedBy(){
               let updated_by=Helper.tryParseInt(this.term.updated_by)
               if(updated_by){
                  Bus.$emit('onShowEditor',updated_by)
               }
                
            },
            
            
            
        },

    }
</script>
