<template>
    <div  class="form-inline">
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
            <select  v-model="params.parent" @change="onParentCourseChanged" style="width:auto;" class="form-control selectWidth">
                 <option v-for="item in parentCourseOptions" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       <div v-if="groupReady"  class="form-group">&nbsp;&nbsp;群組課程
             <select  v-model="params.sub"  @change="onSubCourseChanged" style="width:auto;"  class="form-control selectWidth" >
                  <option v-for="item in subCourseOptions" :value="item.value" v-text="item.text"></option>
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
               
                termOptions:[],
                centerOptions:[],
                parentCourseOptions:[],
                subCourseOptions:[],
                params:{
                    term:0,
                    center:0,
                    parent:0,
                    sub:0,
                },

                course:0,

                parentCourse:{}
             
            }
        },
        // watch: {
        //   ready: function (val) {
        //       if(this.ready){
        //          this.$emit('ready', this.params)
        //       }
        //   },
          
        // },
        computed:{
            groupReady(){
                if(!this.with_course) return false
                if(!this.parentCourse) return false
                return  Helper.tryParseInt(this.parentCourse.credit_count) > 0
                
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
                        this.onReady()
                    }


                })
                .catch(error => {
                    Helper.BusEmitError(error)
                })
             
            },
            loadCourses(){
                let options=Course.options(this.params)
                options.then(data => {
                    this.parentCourseOptions =data.parentOptions
                    this.subCourseOptions = data.subOptions

                    if(data.subOptions.length < 1){
                        this.params.sub=0
                    }


                    this.parentCourse= data.parentCourse
                     let empty={
                          text:'-------',
                          value: 0
                       }
                    this.subCourseOptions.splice(0, 0, empty)   
                    if(this.empty_course){
                       this.parentCourseOptions.splice(0, 0, empty)
                    }

                    let parent =Helper.tryParseInt(this.params.parent)
                    if(!parent){
                        this.params.parent=this.parentCourseOptions[0].value
                    }

                    this.onReady()

                    // let course=this.courseOptions[0]
                    // if(course){
                    //     this.params.course=course.value
                    // }else{
                    //     this.params.course=0
                    // }

                  
                   
                })
                .catch(error => {
                    Helper.BusEmitError(error)
                })
                
            },
            onReady(){
                 let sub = Helper.tryParseInt(this.params.sub)
                 if(sub > 0){
                     this.course=sub
                 }else{
                    this.course=this.params.parent
                 }

                 this.$emit('ready' , this.course)
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
            },
            onParentCourseChanged(){
               this.loadCourses()
            },
            onSubCourseChanged(){
                this.onReady()
            }
            
            
        },

    }
</script>