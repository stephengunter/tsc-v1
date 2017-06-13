<template>
    <div   class="panel panel-default  show-data">
        
        <div class="panel-heading">           
            <span class="panel-title">
                <h4>
                   <i class="fa fa-info-circle" aria-hidden="true"></i>  {{ title }}
                </h4>  
            </span>
            <div>
                <button  v-show="canEdit" @click="btnEditCilcked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                 </button>
               
            </div>           
        </div>
        <div class="panel-body" v-if="loaded">
           
                <div class="row">
                    <div class="col-sm-3">
                        <label class="label-title">報名起始日</label>
                        <p v-text="signup.open_date"></p>  
                      
                    </div>
                    <div class="col-sm-3">
                        <label class="label-title">報名截止日</label>
                        <p>
                        {{ signup.close_date }}

                        </p>  
                      
                    </div>
                    <div class="col-sm-3">
                                              
                        <label class="label-title">人數上限</label>
                        <p v-text="signup.limit"></p>  
                       
                    </div>
                    <div class="col-sm-3">
                        
                    </div>
                </div>
                 <div class="row">
                    <div class="col-sm-3">
                                                
                        <label class="label-title">學費</label>
                        <p v-text="signup.tuition"></p>  
                      
                    </div>
                    <div class="col-sm-3">
                                            
                        <label class="label-title">材料費</label>
                        <p v-text="signup.cost"></p>  
                     
                    </div>
                    <div class="col-sm-3">
                                               
                        <label class="label-title">材料</label>
                        <p v-text="signup.materials"></p>  
                       
                    </div>
                    <div class="col-sm-3">
                         <label class="label-title">網路報名</label>
                        <p v-if="signup.net_signup">
                            可
                        </p>
                        <p v-else>
                            否
                        </p>
                     </div>
                </div>
               
                    
                
          
        </div>
    </div>
</template>

<script>

    export default {
        name: 'ShowSignup',
        props: ['id','canEdit'],
        data() {
            return {
               title:'課程報名資訊',
               signup:{
                    id:0,
                },
               loaded:false
            }
        },
        
        beforeMount() {
            this.init()

        },  
        methods: { 
            getId(){
                return this.signup.id
            },
            setId(){
                if(this.id){
                    this.signup.id=this.id
                }
            },   
            init(){
               
                this.loaded=false
                this.signup={
                    id:0
                }

                this.setId()
                this.fetchData()
                
            },   
            fetchData() {
                let id=this.getId()
                let url = '/api/courses/' + id + '/showSignup'
                
                axios.get(url)
                    .then(response => {
                        let signup = response.data.signup
                        if(signup.cost) signup.cost=Helper.formatMoney(signup.cost)
                        if(signup.tuition)   signup.tuition=Helper.formatMoney(signup.tuition)
                       
                        this.signup = signup
                        this.loaded=true
                       
                    })
                    .catch(function(error) {
                        console.log(error)
                      
                    })
            },
          
            btnEditCilcked(){
               this.$emit('beginEditSignup');
            },
        }
    }
</script>