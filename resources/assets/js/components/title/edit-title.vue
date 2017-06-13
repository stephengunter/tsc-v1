<template>
<tr>
     
     <td v-if="isReadOnly"  v-text="title.name"></td> 
     
     <td v-if="isReadOnly">
         <button class="btn btn-primary btn-xs" @click="beginEdit">
            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        </button>  
         <button  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
             <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
        </button>
     </td> 

     <td v-if="!isReadOnly">
          <input type="text" name="title.name" @keydown="clearErrorMsg('title.name')" class="form-control" v-model="form.title.name">
          <small class="text-danger" v-if="form.errors.has('title.name')" v-text="form.errors.get('title.name')"></small>
    
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
        name: 'EditTitle',
        props: ['title'],
        components: {
            'modal': Modal, 
        },
        data() {
            return {
                isReadOnly:true,
                form: new Form({
                    title: {
                    }
                }),
                
            }
        },
        beforeMount() {
           this.init();
        },
        methods: {
            getId(){
                if(this.title) return this.title.id
                return 0
            },
            init(){  
                this.loaded=false

                if(this.title){
                   this.isReadOnly=true
                   this.form= new Form({
                      title: { }
                   }) 

                }else{
                    this.isReadOnly=false  
                    this.form.title={
                        id:0,
                        name:'',
                    }
                } 

                this.loaded=true            
            },
            showDelete(){
                let id=this.getId()
                return (this.isReadOnly && id>0 );
            },
            beginEdit(){
              
                this.form= new Form({
                    title: this.title
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
                    name:this.title.name
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
                let url = '/api/titles'
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
                            title: title
                        })

                       this.isReadOnly=true;
                      
                       this.$emit('saved', title)
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
