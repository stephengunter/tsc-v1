<template>
    <div  class="form-inline">
       <div class="form-group">
            <select :disabled="disable_terms"  v-model="params.term" @change="onTermChanged"   style="width:auto;" class="form-control selectWidth">
                <option v-for="(item,index) in termOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       <div class="form-group">
            <select :disabled="disable_centers" v-model="params.center" @change="onCenterChanged" style="width:auto;" class="form-control selectWidth">
                 <option v-for="(item,index) in centerOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
       </div>
       <div class="form-group">
            <select  v-model="course" @change="onCourseChanged" style="width:auto;" class="form-control selectWidth">
                 <option v-for="(item,index) in courseOptions" :key="index" :value="item.value" v-text="item.text"></option>
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
            disable_terms:{
                type: Boolean,
                default: false
            },
            disable_centers:{
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

                courseOptions:[],
               
                params:{},
                   

                course:0,

              
             
            }
        },
        computed:{
           
            
        }, 
        watch: {
            search_params: {
                handler: function () {
                    let changed=false
                    if(this.search_params.term!=this.params.term){
                       this.params.term = this.search_params.term
                       changed=true
                    }
                    if(this.search_params.center!=this.params.center){
                        this.params.center = this.search_params.center
                        changed=true
                    }

                    if(changed) this.loadCourses()
                 
                 
                
                },
                deep: true
            },
            

        },
        beforeMount() {
            this.setParams()
            
            this.init()
        },
        methods: {
            setParams(){
                if(this.search_params){
                    for(let property in this.search_params){
                        this.params[property]=this.search_params[property]
                        }
                    }
                    
                    let defaults=[
                        'term','center'
                    ]

                    defaults.forEach((key)=>{
                        if(!this.params.hasOwnProperty(key)){
                            this.params[key] = 0
                        }
                })
            },
            setDefaultParam(key){
               if(this.search_params.hasOwnProperty(key)) return
               this.search_params[key]=0
            },
            init(){
                let options=Signup.indexOptions()
                    options.then(data => {
                        this.termOptions = data.termOptions

                        if(!this.params.term){
                            let term=this.termOptions[0].value
                            this.params.term=term
                        }

                        this.centerOptions = data.centerOptions                        
                        if(!this.params.center){
                            let center=this.centerOptions[0].value
                            this.params.center=center
                        }

                    
                        this.loadCourses()
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                    })
             
            },
            loadCourses(){

                let options=Course.options(this.params)
                options.then(data => {
                    this.courseOptions = data.options 

                    if(data.options.length)
                    {
                        this.course=data.options[0].value
                    }else{
                        this.course= 0
                    }
                   
                    this.onReady()
                   
                })
                .catch(error => {
                    Helper.BusEmitError(error)
                })
                
            },
            onReady(){
               this.$emit('ready' , this.course)
            },
            
            onTermChanged(){
              
                this.loadCourses()
                
            },
            onCenterChanged(){
                this.loadCourses()
               
            },
            onCourseChanged(){
                this.onReady()
            },
          
            
        },

    }
</script>