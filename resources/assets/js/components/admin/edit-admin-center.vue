<template>
<tr>
     
     <td v-if="isReadOnly"  v-text="center.name"></td> 
     
     <td v-if="isReadOnly">
         
         <button v-if="center.canDelete"  class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
           <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
        </button>
     </td> 

     <td v-if="!isReadOnly">
          <select  v-model="form.center_id"   style="width:auto;" class="form-control selectWidth">
               <option v-for="center in centers" :value="center.id" v-text="center.name"></option>
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
        name: 'EditAdminCenter',
        props: ['center','admin_id','centers'],
        components: {
            'modal': Modal, 
        },
        data() {
            return {
                isReadOnly:true,
                form: new Form({
                    center_id:0,
                    admin_id:0
                }),
                centerOptions:[]
                
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
                this.loaded=false

                if(this.center){
                   this.isReadOnly=true
                   this.loaded=true  
                }else{
                    this.isReadOnly=false  
                    if(this.centers){
                        this.form=new Form({
                          center_id:this.centers[0].id,
                          admin_id:this.admin_id
                       })
                    }
                } 
                          
            },
            showDelete(){
                let id=this.getId()
                return (this.isReadOnly && id>0 );
            },
            cancelEdit(){
               
                if(this.center){
                     this.isReadOnly=true
                }else{
                    this.$emit('endEdit')
                }
                
            },
            btnDeleteClicked(){
                let values={
                    id:this.getId(),
                    name:this.center.name
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
            submitForm(){
                let url = '/api/centeradmin'
                let method = 'post'
               
                this.form.submit(method,url)
                    .then(result => {
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
