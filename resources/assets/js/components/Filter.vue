<template>
    <div class="form-inline">
                
        <div  v-if="hasSearchKey('term')" class="form-group">
            <select @change="onParamChanged" v-model="search_params.term"    style="width:auto;" class="form-control selectWidth">
                <option v-for="(item,index) in termOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
        </div>
        <div v-if="hasSearchKey('center')"  class="form-group">
            <select @change="onParamChanged"  v-model="search_params.center" style="width:auto;" class="form-control selectWidth">
                <option v-for="(item,index) in centerOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
        </div> 
        <div v-if="hasSearchKey('category')" class="form-group">
            <select @change="onParamChanged"  v-model="search_params.category" style="width:auto;" class="form-control selectWidth">
                <option v-for="(item,index)  in categoryOptions" :key="index"  :value="item.value" v-text="item.text"></option>
            </select>
        </div>
        <div v-if="hasSearchKey('weekday')" class="form-group">
            <select @change="onParamChanged" v-model="search_params.weekday" style="width:auto;" class="form-control selectWidth">
                <option v-for="(item,index) in weekdayOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
        </div>

        <div v-if="hasParent()" class="form-group">&nbsp;&nbsp;群組課程：
            <span>
            &nbsp; {{ parent_course.name  }}
                <button type="button" @click.prevent="clearParent" class="close">
                    <span aria-hidden="true">×</span>
                </button>
            </span>
        </div> 
        
        

    </div>
</template>

<script>
    export default {
        name: 'Filter',
        props: {
            params:{
                type: Object,
                default: null
            },
            options:{
                type: Object,
                default: null
            },
            parent_course:{
                type: Object,
                default: null
            },
            
        },
        beforeMount() {
            this.init()
        },
        data() {
            return {
                ready:false,
                search_params:{},

                termOptions:[],
                categoryOptions:[],
                centerOptions:[],
                weekdayOptions:[],

                parentCourse:null,
             
            }
        },
        computed: {
            
        },
        methods: {
            init() {
                if(!this.params) return false
                for(let property in this.params){
                    this.search_params[property]=this.params[property]
                }
                
                if(this.options){
                    this.setOptions(this.options)
                }else{
                    this.loadOptions()
                }
                
                
            },
            loadOptions(){
                let options=Course.indexOptions()
                options.then(data=>{
                    this.setOptions(data)
                }).catch(error=>{
                    Helper.BusEmitError(error)
                    this.ready=false
                })
            },
            setOptions(data){
                let search_params=this.search_params

                if(search_params.hasOwnProperty('term')){
                    this.termOptions=data.termOptions
                    if(!search_params.term) search_params.term=this.termOptions[0].value
                }

                if(search_params.hasOwnProperty('center')){
                    this.centerOptions=data.centerOptions
                    if(!search_params.center){
                        let allCenters={ text:'全部開課中心' , value:'0' }
                        this.centerOptions.splice(0, 0, allCenters)
                        search_params.center=this.centerOptions[0].value
                    } 
                }

                if(search_params.hasOwnProperty('category')){
                    this.categoryOptions=data.categoryOptions
                    if(!search_params.category){
                        let defaultItem={ text:'全部課程分類' , value:'0' }
                        this.categoryOptions.splice(0, 0, defaultItem)
                        search_params.category=this.categoryOptions[0].value
                    } 
                }

                if(search_params.hasOwnProperty('weekday')){
                    this.weekdayOptions=data.weekdayOptions
                    if(!search_params.weekday){
                        let allWeekdays={ text:'不限星期' , value:'0' }
                        this.weekdayOptions.splice(0, 0, allWeekdays)
                        search_params.weekday=this.weekdayOptions[0].value
                    } 
                }

                this.ready=true
                this.$emit('ready')
            },
            hasSearchKey(key){
                return  this.search_params.hasOwnProperty(key)
            },
            hasParent(){
                if(!this.parent_course) return false
                  
                return  Helper.tryParseInt(this.parent_course.id) > 0
            },
            clearParent(){
                this.search_params.parent=0
                this.$emit('clear-parent', this.search_params)
            },
            onParamChanged(){
                if(this.hasParent()){
                    this.search_params.parent=this.parent_course.id
                }
                this.$emit('param-changed' , this.search_params)
            },
            
            
           
        },

    }
</script>