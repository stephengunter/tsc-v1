<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="form-inline">
                
                <div class="form-group">
                    <select  v-model="params.term"    style="width:auto;" class="form-control selectWidth">
                        <option v-for="item in termOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select  v-model="params.center" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                
                

            </div>
        </div>
    </div>
     
    <status-list v-if="ready" :search_params="params"  :hide_create="hide_create" :version="version"  
        :can_select="can_select"
        @selected="onSelected">
    </status-list>

</div>

</template>

<script>
    import CourseStatusList from '../../components/course/status/list.vue'
    

    export default {
        name: 'CourseStatusIndex',       
        components: {
            'status-list':CourseStatusList
        },
        props: {
            version: {
              type: Number,
              default: 0
            },
            hide_create:{
               type: Boolean,
               default: false
            }
        },
        data() {
            return {
                ready:false,
                termOptions:[],
                centerOptions:[],
                params:{
                    term:0,
                    center:0,
                 
                },
                can_select:false,
             
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                let options=Course.indexOptions()
                options.then(data=>{
                    this.termOptions=data.termOptions
                    this.params.term=this.termOptions[0].value

                    this.centerOptions=data.centerOptions                   
                    this.params.center=this.centerOptions[0].value

                    this.ready=true
                }).catch(error=>{
                    Helper.BusEmitError(error)
                    this.ready=false
                })
             
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            
            
        },

    }
</script>