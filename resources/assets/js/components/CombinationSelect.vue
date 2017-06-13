<template>
    <div v-show="ready" class="form-inline">
       <div class="form-group">
            <select  v-model="params.term" @change="onTermChanged"   style="width:auto;" class="form-control selectWidth">
                <option v-for="item in termOptions" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       <div class="form-group">
            <select  v-model="params.center" @change="onCenterChanged" style="width:auto;" class="form-control selectWidth">
                 <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       <div v-if="with_course" class="form-group">
            <select  v-model="params.course" @change="onCourseChanged" style="width:auto;" class="form-control selectWidth">
                 <option v-for="item in courseOptions" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       
    </div>

</template>

<script>
    export default {
        name: 'CombinationSelect',  
        props: {
            with_course: {
              type: Boolean,
              default: false
            },
            empty_course: {
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                ready:false,
                termOptions:[],
                centerOptions:[],
                courseOptions:[],
                params:{
                    term:0,
                    center:0,
                    course:0,
                },
             
            }
        },
        watch: {
          ready: function (val) {
              if(this.ready){
                 this.$emit('ready', this.params)
              }
          },
          
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){

                let options=Signup.indexOptions()
                options.then(data => {
                    this.termOptions = data.termOptions
                    let term=this.termOptions[0].value
                    this.params.term=term

                    this.centerOptions = data.centerOptions                        
                    let center=this.centerOptions[0].value
                    this.params.center=center

                    if(this.with_course){
                        this.loadCourses()
                    }else{
                      this.ready=true
                    }


                })
                .catch(error => {
                    Helper.BusEmitError(error)
                    this.ready=false
                })
             
            },
            loadCourses(){
                this.ready=false
                let options=Course.options(this.params)
                options.then(data => {
                    this.courseOptions =data.options
                    if(this.empty_course){
                       let empty={
                          text:'-------',
                          value: 0
                       }
                       this.courseOptions.splice(0, 0, empty);
                    }

                    this.ready=true

                    let course=this.courseOptions[0]
                    if(course){
                        this.params.course=course.value
                    }else{
                        this.params.course=0
                    }
                   
                })
                .catch(error => {
                    this.ready=false
                    Helper.BusEmitError(error)
                })
                
            },
            onTermChanged(){
               this.$emit('term-changed', this.params.term)
               if(this.with_course){
                   this.loadCourses()
               }
            },
            onCenterChanged(){
                this.$emit('center-changed', this.params.center)
                if(this.with_course){
                   this.loadCourses()
               }
            },
            onCourseChanged(){
                this.$emit('course-changed', this.params.course)
            }
            
            
        },

    }
</script>