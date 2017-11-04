<template>
     <div>
         <button @click="test">test</button>
         <selector ref="ppp" :params="params" :options="filter_options" :title_text="title_text">
             <span slot="btn">
                  <span  class="glyphicon glyphicon-ok" aria-hidden="true"></span> 
                   審核通過
             </span>
         </selector>
     </div>
</template>

<script>
    import Selector from '../components/course/selector.vue'
    export default {
        name:'Test',
        components: {
           'selector':Selector 
        },
        data() {
            return {
                ready:false,
                params:{
                    reviewed: 0,
                    center : 0,
                    parent:0
                },
                
                filter_options:{
                    centerOptions:  [
                        { value:'1', text:'花'},
                        { value:'2', text:'桃'}
                    ]
                },

                title_text:'課程審核'
            }
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
               this.params.center=this.filter_options.centerOptions[0].value
            },
            fetchData() {
                
            }, 
            test(){
                //cument.getElementById("ext
                this.$refs.ppp.refresh()
            }
            
            
            
            
        }
    }
</script>
