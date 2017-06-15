<template>

    <div v-if="signupinfo" class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
               <i class="fa fa-info-circle" aria-hidden="true"></i>  課程報名資訊
            </span> 
              
            <div>
                
                <button v-if="signupinfo.canEdit" v-show="can_edit" @click="btnEditCilcked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                </button>
               
               
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body" >
           
            <div class="row">
                    <div class="col-sm-3">
                        <label class="label-title">報名起始日</label>
                        <p v-text="signupinfo.open_date"></p>  
                      
                    </div>
                    <div class="col-sm-3">
                        <label class="label-title">報名截止日</label>
                        <p>
                        {{ signupinfo.close_date }}

                        </p>  
                      
                    </div>
                    <div class="col-sm-3">
                                              
                        <label class="label-title">人數上限</label>
                        <p v-text="signupinfo.limit"></p>  
                       
                    </div>
                    <div class="col-sm-3">
                        
                    </div>
            </div>
            <div class="row">
                    <div class="col-sm-3">
                                                
                        <label class="label-title">學費</label>
                        <p v-text="signupinfo.tuition"></p>  
                      
                    </div>
                    <div class="col-sm-3">
                                            
                        <label class="label-title">材料費</label>
                        <p v-text="signupinfo.cost"></p>  
                     
                    </div>
                    <div class="col-sm-3">
                                               
                        <label class="label-title">材料</label>
                        <p v-text="signupinfo.materials"></p>  
                       
                    </div>
                    <div class="col-sm-3">
                         <label class="label-title">網路報名</label>
                        <p v-if="signupinfo.net_signupinfo">
                            可
                        </p>
                        <p v-else>
                            否
                        </p>
                     </div>
            </div>
          
        </div> <!-- End panel-heading-->

    </div>

    
 

</div>
</template>
<script>
    export default {
        name: 'ShowSignupInfo', 
        props: {
            signupinfo: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
        },
        data() {
             return {
               
            }
        },
         
        methods: {  
            showUpdatedBy(){
               let updated_by=Helper.tryParseInt(this.signupinfo.updated_by)
               if(updated_by){
                  Bus.$emit('onShowEditor',updated_by)
               }
                
            },
            btnEditCilcked(){
               this.$emit('begin-edit');
            },
            

            
        }
    }
</script>