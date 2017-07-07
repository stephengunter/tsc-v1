<template>
<tr v-if="readOnly" >
    
     <td v-text="discount.name"></td> 
     <td v-text="identityName()"></td> 
     <td v-text="discount.points"></td> 
     <td v-text="discount.ps"></td>
     <td v-html="$options.filters.activeLabel(discount.active)" ></td>  
     <td> 
        <a v-if="discount.updated_by"  href="#" @click.prevent="showUpdatedBy" >
          {{   discount.updated_at|tpeTime  }}
        </a>
        <span v-else>{{   discount.updated_at|tpeTime  }}</span>
        

     </td>  
     <td>
         <button class="btn btn-primary btn-xs" @click="onBeginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="discount.canDelete"  class="btn btn-danger btn-xs" @click.prevent="onBtnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 
</tr>
<tr v-else>
    
    <td v-if="loaded">
        <input type="text" name="discount.name" @keydown="clearErrorMsg('discount.name')" class="form-control" v-model="form.discount.name">
        <small class="text-danger" v-if="form.errors.has('discount.name')" v-text="form.errors.get('discount.name')"></small>
    </td>
    <td v-if="loaded">
        <select   v-model="form.discount.identity_id"  class="form-control" >
               <option v-for="item in identityOptions" :value="item.value" v-text="item.text"></option>
        </select>
    </td>
    <td v-if="loaded">
        <select   v-model="form.discount.points"  class="form-control" >
            <option v-for="item in pointOptions" :value="item.value" v-text="item.text"></option>
        </select>
    </td>
    <td v-if="loaded">
        <textarea rows="5" cols="50" name="discount.ps" class="form-control" v-model="form.discount.ps"> </textarea>
    </td>
    <td v-if="loaded">
        <div>
            <input type="hidden" v-model="form.discount.active"  >
            <toggle :items="activeOptions"   :default_val="form.discount.active" @selected=setActive></toggle>
        </div>
    </td>
    <td v-if="loaded">

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
        name: 'EditDiscount',
        props: {
            discount: {
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
                identityOptions:[],
                pointOptions:[],

                activeOptions:[]
           
            }
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.discount) return Helper.tryParseInt(this.discount.id)
                return 0
            },            
            init(){  
                if(this.discount){
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
                    getData=Discount.edit(id)
                }else{
                    getData=Discount.create()
                }
                getData.then(data => {
                    this.form=new Form({
                        discount:data.discount
                    }) 
                    this.identityOptions=data.identityOptions
                    this.loadOptions(id)
                    
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            loadOptions(id){
                this.activeOptions=Helper.activeOptions()
                this.pointOptions=Discount.pointOptions()

                if(!id){
                    this.form.discount.identity_id=this.identityOptions[0].value
                    this.form.discount.points=this.pointOptions[0].value
                }
                this.loaded=true
            },
            identityName(){
                if(this.discount.identity) return this.discount.identity.name
                    return ''
            },
            setActive(val){
                this.form.discount.active=val
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
                    name:this.discount.name
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
                    save=Discount.update(this.form, id)
                }else{
                    save=Discount.store(this.form)
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
