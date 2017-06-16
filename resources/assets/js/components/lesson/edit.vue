<template>

    <div class="panel panel-default">
        
        <div class="panel-heading">    
             <span class="panel-title">
                   <h4 v-html="title"></h4>
             </span>           
        </div>
        <div class="panel-body">
            <form v-if="loaded" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>課程名稱</label>
                            <input  name="course_name" :value="course_name" disabled>
                        </div>
                    </div>
                </div> <!-- end row-->
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">                           
                            <button type="submit"  class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-default" @click.prevent="onCanceled">取消</button>  
                        </div>
                    </div>
      
                </div> <!-- end row-->

            </form>
        </div>
    </div>
    
</template>
<script>
    
    export default {
        name: 'EditLesson',
        props: {
            id: {
              type: Number,
              default: 0
            },
            course_id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                icon:Helper.getIcon(Lesson.title()),
                title:'',
                loaded:false,
                form: new Form({
                    lesson: {
                      
                    }
                }),
            }
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                this.loaded=false
                this.form = new Form({
                    lesson: {
                    }
                })
                this.fetchData() 
            },
            fetchData() {
                let getData=null
                if(this.id){
                    getData=Lesson.edit(this.id)
                }else{
                    getData=Lesson.create(this.course_id)
                }
                getData.then(data=>{
                   this.form.lesson=data.lesson
                   this.loaded=true 
                }).catch(error=>{
                   Helper.BusEmitError(error)  
                   this.loaded=false
                })  
            },
            setActive(val){
                this.form.lesson.active=val
            },
            setReviewed(val){
                this.form.lesson.reviewed=val
            },
            
            clearErrorMsg(name) {
                this.form.errors.clear(name)
            },
            onSubmit() {
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