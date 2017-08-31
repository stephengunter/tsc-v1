<template>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4>成績匯入預覽</h4>
            </span>
            
            <div>
                <button class="btn btn-success btn-sm" >
                    存檔
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div  class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 90%;">
                <thead> 
                    <tr v-if="creating"> 
                       
                        <th>學員編號</th> 
                        <th>學員姓名</th> 
                        <th>成績</th> 
                        <th>備註</th>
                    </tr> 
                </thead>
                <tbody> 
                    <tr>
                        
                    </tr>
                       

                       
                        
                </tbody>
                    
            </table>
        </div><!-- End panel-body-->

    </div>   
</template>


<script>
    export default {
        name: 'ImportScores',
        props: {
            scores: {
               type: Array,
               default: []
            },
            
        },
        beforeMount() {
          
        },
        methods: {
            
            onSubmit() {
                this.submitForm()
            },
            submitForm() {
                let save = null
                let id=this.getId()
                if(id){
                    save=Holiday.update(this.form, id)
                }else{
                    save=Holiday.store(this.form)
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
