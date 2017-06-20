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
                        <label class="label-title">報名狀態</label>
                        <p v-html="status.signupLabel"></p>  
                      
                    </div>
                    <div class="col-sm-3">
                        <label class="label-title">註冊狀態</label>
                        <p v-html="status.registerLabel"></p> 
                      
                    </div>
                    <div class="col-sm-3">
                                              
                        <label class="label-title">開課狀態</label>
                         <p v-html="status.classLabel"></p> 
                       
                    </div>
                    <div class="col-sm-3">
                        
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
                    this.status = new  CourseStatus(data.status) 
                    this.loaded = true 
                                     
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
              
            },
            btnEditCilcked(){
               this.$emit('begin-edit');
            },
            

            
        }
    }
</script>