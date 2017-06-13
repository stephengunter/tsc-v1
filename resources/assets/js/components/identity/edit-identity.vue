<template>
<tr>
     
     <td v-if="isReadOnly"  v-text="identity.name"></td>
     <td v-if="isReadOnly"  v-text="isMemberText()"></td>
     <td v-if="isReadOnly"  v-text="identity.ps"></td> 
     
     <td v-if="isReadOnly">
         <button class="btn btn-primary btn-xs" @click="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button v-if="identity.canDelete"  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 

     <td v-if="!isReadOnly">
          <input type="text" name="identity.name" @keydown="clearErrorMsg('identity.name')" class="form-control" v-model="form.identity.name">
          <small class="text-danger" v-if="form.errors.has('identity.name')" v-text="form.errors.get('identity.name')"></small>
    
     </td>
     <td v-if="!isReadOnly">
         <input type="hidden" v-model="form.identity.member"  >
            <toggle :items="boolOptions"   :defaultVal="form.identity.member" @selected=setMember></toggle>
          
     </td>
     <td v-if="!isReadOnly">
         <textarea rows="5" cols="50" name="identity.ps" class="form-control" v-model="form.identity.ps"> </textarea>
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
        name: 'EditIdentity',
        props: ['identity'],
        components: {
            'modal': Modal, 
            Toggle
        },
        data() {
            return {
                isReadOnly:true,
                form: new Form({
                    identity: {
                    }
                }),
                boolOptions: [{
                    text: '是',
                    value: '1'
                }, {
                    text: '否',
                    value: '0'
                }],
                
            }
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.identity) return this.identity.id
                return 0
            },
            init(){  
                this.loaded=false

                if(this.identity){
                   this.isReadOnly=true
                   this.form= new Form({
                      identity: { }
                   }) 

                }else{
                    this.isReadOnly=false  
                    this.form.identity={
                        id:0,
                        name:'',
                        member:0
                    }
                } 

                this.loaded=true            
            },
            isMemberText(){
                if(parseInt(this.identity.member)>0) return '是'
                    return ''
            },
            showDelete(){
                let id=this.getId()
                return (this.isReadOnly && id>0 );
            },
            beginEdit(){
              
                this.form= new Form({
                    identity: this.identity
                }) 
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
                    name:this.identity.name
                }
                this.$emit('btnDeleteClicked' , values)
            },
            setMember(val) {
                this.form.identity.member = val;
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
                let url = '/api/identities'
                let method = 'post'
                let id=this.getId()
                if(id){
                    method='put'
                    url += '/' + id   
                }
               
                this.form.submit(method,url)
                    .then(classroom => {
                       Helper.BusEmitOK()

                       this.form = new Form({
                            identity: identity
                        })

                       this.isReadOnly=true;
                      
                       this.$emit('saved', identity)
                    })
                    .catch(error => {
                       Helper.BusEmitError(error)
                           
                    })
            },
            
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            
            
        },

    }
</script>
