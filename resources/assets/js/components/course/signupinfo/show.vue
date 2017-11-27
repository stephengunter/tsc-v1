<template>

    <div v-if="signupinfo" class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4>
                     <i class="fa fa-info-circle" aria-hidden="true"></i>  課程報名資訊
                </h4>
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
                                            
                    <label class="label-title">人數上限</label>
                    <p v-text="signupinfo.limit"></p>  
                    
                </div>
                <div class="col-sm-3">
                    <label class="label-title">最低人數</label>
                    <p v-text="signupinfo.min"></p>  
                </div>
                <div class="col-sm-3">
                    <label class="label-title">網路報名</label>
                    <p>
                         {{ signupinfo.canNetSignup }}
                        <span v-html="$options.filters.yesOrNoSign(signupinfo.discount)"></span>    
                    </p>  
                </div>
                <div class="col-sm-3">
                                            
                    <label class="label-title">折扣優惠</label>
                    <p>
                         {{ signupinfo.canDiscount }}
                        <span v-html="$options.filters.yesOrNoSign(signupinfo.discount)"></span>    
                    </p>  
                    
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                                            
                    <label class="label-title">學費</label>
                    <p v-text="signupinfo.tuition"></p>  
                    
                </div>
                 
                <div class="col-sm-3">
                                        
                    <label class="label-title">教材費</label>
                    <p v-text="signupinfo.cost"></p>  
                    
                </div>
                <div class="col-sm-6">
                                        
                     <label class="label-title">教材</label>
                    <p v-html="signupinfo.materials"></p>  
                    
                </div>
                
            </div>
           
            <div class="row">
                <div class="col-sm-12">
                                            
                    <label class="label-title">注意事項</label>
                    <p v-html="signupinfo.caution"></p>  
                    
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
           
            btnEditCilcked(){
               this.$emit('begin-edit');
            },
            

            
        }
    }
</script>