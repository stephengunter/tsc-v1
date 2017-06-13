<template>
<tr>
     <td v-if="isReadOnly"  v-text="discount.name"></td> 
     <td v-if="isReadOnly"  v-text="identityName()"></td> 
     <td v-if="isReadOnly"  v-text="discount.points"></td> 
     <td v-if="isReadOnly"  v-text="discount.ps"></td>
     <td v-if="isReadOnly" v-html="$options.filters.activeLabel(discount.active)" ></td> 
    
     <td v-if="isReadOnly"> 
        <a v-if="discount.updated_by"  href="#" @click.prevent="showUpdatedBy" >
          {{   discount.updated_at|tpeTime  }}
        </a>
        <span v-else>{{   discount.updated_at|tpeTime  }}</span>
        

     </td>
    
     <td v-if="isReadOnly">
         <button  class="btn btn-primary btn-xs" @click="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="discount.canDelete"  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 




    <td v-if="!isReadOnly">
          <input type="text" name="discount.name" @keydown="clearErrorMsg('discount.name')" class="form-control" v-model="form.discount.name">
          <small class="text-danger" v-if="form.errors.has('discount.name')" v-text="form.errors.get('discount.name')"></small>
    
     </td>
     <td v-if="!isReadOnly">
          <select   v-model="form.discount.identity_id"  class="form-control" >
               <option v-for="item in identityOptions" :value="item.value" v-text="item.text"></option>
           </select>
     </td> 
     <td v-if="!isReadOnly">
          <select   v-model="form.discount.points"  class="form-control" >
               <option v-for="item in pointOptions" :value="item.value" v-text="item.text"></option>
           </select>
     </td>
     <td v-if="!isReadOnly">
         <textarea rows="5" cols="50" name="discount.ps" class="form-control" v-model="form.discount.ps"> </textarea>
     </td>

     <td v-if="!isReadOnly">
          <div>
            <input type="hidden" v-model="form.discount.active"  >
            <toggle :items="activeOptions"   :defaultVal="form.discount.active" @selected=setActive></toggle>
          
     </td>
     <td v-if="!isReadOnly">
         
     </td>
     <td v-if="!isReadOnly">
         
        <button @click.prevent="onSubmit" :disabled="form.errors.any()"  class="btn btn-success btn-xs">
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
        name: 'EditDiscount',
        props: ['discount'],
        components: {
            'toggle': Toggle,
        },
        data() {
            return {
                isReadOnly:true,
                form: new Form({
                    discount: {
                        id:0,
                        name:'',
                        points:95,                        
                        active:false
                    }
                }),

                identityOptions:[],
                pointOptions:[],

                activeOptions: [{
                    text: '上架中',
                    value: '1'
                }, {
                    text: '已下架',
                    value: '0'
                }],
                
            }
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.discount) return this.discount.id
                return 0
            },
            init(){  
                this.loaded=false

                if(this.discount){
                    this.isReadOnly=true                   
                }else{
                    this.beginEdit()
                } 

                this.loaded=true            
            },
            identityName(){
                if(this.discount.identity) return this.discount.identity.name
                    return ''
            },
            loadIdentityOptions(){
                let url = '/api/identities/options'
                axios.get(url)
                    .then(response => {
                        let options=response.data.options
                        
                        this.identityOptions=options
                      
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
               
            },
            loadPointOptions(){
                let options = []
                let max = 95
                let min=35
                for (var i = max; i >= min; i-=5) {
                    let option = {
                        text: i,
                        value: i
                    }
                    options.push(option)
                }

                this.pointOptions=options

            },
            showDelete(){
                let id=this.getId()
                return (this.isReadOnly && id>0 );
            },
            beginEdit(){
               if(this.discount){
                   this.form.discount = this.discount

                }else{
                  
                     this.form.discount={
                        id:0,
                        name:'',
                        points:95,
                        active:false
                    } 
                  
                } 
                this.loadIdentityOptions()
                this.loadPointOptions()
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
                    name:this.discount.name
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
                let url = '/api/discounts'
                let method = 'post'
                let id=this.getId()
                if(id){
                    method='put'
                    url += '/' + id   
                }
               
                this.form.submit(method,url)
                    .then(discount => {

                      Helper.BusEmitOK() 

                       this.form = new Form({
                            discount: discount
                        })

                       this.isReadOnly=true;
                      
                       this.$emit('saved', discount)
                    })
                    .catch(error => {
                        
                        Helper.BusEmitError(error)
                           
                    })
            },
            
            setActive(val) {
                this.form.discount.active = val;
            },
            
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.discount.updated_by)
            }
            
            
        },

    }
</script>
