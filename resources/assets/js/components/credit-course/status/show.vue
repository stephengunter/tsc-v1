<template>

    <div v-if="loaded" class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
              
            <div>
                <button v-if="status.canEdit" v-show="can_edit" @click="btnEditCilcked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                </button>
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body" >
           
            <div class="row">
                <div class="col-sm-3">
                    <label class="label-title">狀態</label>
                    <p v-html="activeLabel(status.course.active)"></p> 
                </div>
                <div class="col-sm-3">
                    <label class="label-title">報名</label>
                    <p v-html="signupLabel()"></p>  
                    
                </div>
                <div class="col-sm-3">
                    <label class="label-title">註冊</label>
                    <p v-html="registerLabel()"></p>  
                    
                </div>
                <div class="col-sm-3">
                                            
                    <label class="label-title">課程進行</label>
                    <p v-html="classLabel()"></p>  
                    
                </div>
                
            </div> <!-- End row-->
            <div class="row">
                <div class="col-sm-12">
                    <label class="label-title">備註</label>
                    <p v-text="status.ps"></p>  
                </div>
            </div>    
        </div> <!-- End panel-heading-->

    </div>

    
 

</div>
</template>
<script>
    export default {
        name: 'ShowCourseStatus', 
        props: {
            course_id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },  
           
        },
        data() {
            return {
                loaded:false,
                title:Helper.getIcon(CourseStatus.title()) + '  課程狀態',
                status:null
            }
        },
        beforeMount(){
           this.init()
        }, 
        methods: {  
            init(){
               this.loaded=false
               if(this.course_id) this.fetchData()
              
            },
            fetchData(){
                let id=this.course_id
                let getData =CourseStatus.show(id)
                getData.then(data => {

                    this.status =data.status
       
                    this.loaded = true 
                                     
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
              
            },
            activeLabel(active){
               
                return Course.activeLabel(active)
            },
            signupLabel(){
                return CourseStatus.getSignupLabel(this.status)
            },
            registerLabel(){
                return CourseStatus.getRegisterLabel(this.status)
            },
            classLabel(){
                return CourseStatus.getClassLabel(this.status)
            },
            btnEditCilcked(){
               this.$emit('begin-edit');
            },
            

            
        }
    }
</script>