<template>
    <div  class="form-inline">
       <div class="form-group">
            <select  v-model="params.term" @change="onTermChanged"   style="width:auto;" class="form-control selectWidth">
                <option v-for="(item,index) in termOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       <div class="form-group">
            <select  v-model="params.center" @change="onCenterChanged" style="width:auto;" class="form-control selectWidth">
                 <option v-for="(item,index) in centerOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       <div class="form-group">
            <select  v-model="params.parent" @change="onParentCourseChanged" style="width:auto;" class="form-control selectWidth">
                 <option v-for="(item,index) in parentCourseOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       <div v-if="groupReady"  class="form-group">&nbsp;&nbsp;群組課程
             <select  v-model="params.sub"  @change="onSubCourseChanged" style="width:auto;"  class="form-control selectWidth" >
                  <option v-for="(item,index) in subCourseOptions" :key="index" :value="item.value" v-text="item.text"></option>
             </select>
        </div>
     
    </div>



</template>

<script>
    export default {
        name: 'CombinationSelect',  
        props: {
            search_params:{
                type: Object,
                default: null
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
                params:{},
                   

                course:0,

                parentCourse:{}
             
            }
        },
        computed:{
            groupReady(){
               
                if(!this.parentCourse) return false
                return  Helper.tryParseInt(this.parentCourse.credit_count) > 0
                
            },
            
        }, 
        beforeMount() {
            if(this.search_params){
                for(let property in this.search_params){
                    this.params[property]=this.search_params[property]
                }
            }
            
            let defaults=[
                'term','center','parent','sub'
            ]

            defaults.forEach((key)=>{
                if(!this.params.hasOwnProperty(key)){
                    this.params[key] = 0
                }
            })
            
            this.init()
        },
        methods: {
            setDefaultParam(key){
               if(this.search_params.hasOwnProperty(key)) return
               this.search_params[key]=0
            },
            init(){
                

                let options=Signup.indexOptions()
                options.then(data => {
                    this.termOptions = data.termOptions
                    let term=this.termOptions[0].value
                    this.params.term=term

                    this.centerOptions = data.centerOptions                        
                    let center=this.centerOptions[0].value
                    this.params.center=center

                    this.params.parent=0
                    this.params.sub=0
                    this.loadCourses()
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
                        this.params.parent=0
                        if(this.parentCourseOptions.length){
                             this.params.parent=this.parentCourseOptions[0].value
                        }
                       
                    }

                    this.onReady()
                   
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
                this.params.parent=0
                this.params.sub=0
                this.loadCourses()
                
            },
            onCenterChanged(){
                
                this.params.parent=0
                this.params.sub=0
                this.loadCourses()
               
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