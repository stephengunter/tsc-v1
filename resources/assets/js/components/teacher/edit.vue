<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">    
             <span class="panel-title">
                   <h4 v-html="title"></h4>
             </span>   
              <toggle :items="typeOptions"   :default_val="form.teacher.group" @selected=onTypeSelected></toggle>
                     
        </div>
        <div v-if="group" class="panel-body">group
        </div>
        <div v-else class="panel-body">
            <form v-if="loaded" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form">
                <teacher-inputs :form="form"
                  :with_user="inputsSettings.with_user" :with_teacher_name="inputsSettings.with_teacher_name"
                  :with_profile="inputsSettings.with_profile"
                  @active-selected="setActive"   @reviewed-selected="setReviewed"
                  @canceled="onCanceled" >
                </teacher-inputs>

            </form>
        </div>
       
    </div>
    
</template>
<script>
    import Inputs from '../../components/teacher/inputs.vue'
    export default {
        name: 'EditTeacher',
        components: {
            'teacher-inputs':Inputs
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            group:{
               type:Boolean,
               default:false

            }
        },
        data() {
            return {
                title:Helper.getIcon('Teachers')  + '  編輯教師資料',
                loaded:false,
                form: new Form({
                    teacher: {
                      
                    }
                }),
                inputsSettings:{
                   with_user:false,
                   with_profile:false,
                   with_teacher_name:true
                },
                typeOptions:[{
                    text: '個人教師',
                    value: '0'
                }, {
                    text: '教師群組',
                    value: '1'
                }]
               

            }
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                
                this.fetchData() 
            },
            fetchData() {
                let getData=Teacher.edit(this.id)  
                   getData.then(data=>{
                       this.form.teacher=data.teacher
                       this.loaded=true 
                   }).catch(error=>{
                       Helper.BusEmitError(error)  
                       this.loaded=false
                   })  
            },
            onTypeSelected(val){
               this.form.teacher.group=Helper.isTrue(val)
               
               
            },
            setActive(val){
                this.form.teacher.active=val
            },
            setReviewed(val){
                this.form.teacher.reviewed=val
            },
            
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
              if(this.form.teacher.experiences){
                let experience=Helper.replaceAll( this.form.teacher.experiences, '\n','<br>')
                this.form.teacher.experiences=experience
              }
                
             
                this.submitForm()
            },
            submitForm() {
                let update=Teacher.update(this.form , this.id)
                
                update.then(data => {
                   Helper.BusEmitOK()
                   this.$emit('saved',data)                            
                })
                .catch(error => {
                    Helper.BusEmitError(error) 
                })
            },
         
           
            onCanceled(){
                this.$emit('canceled')
            }




        },

    }
</script>