<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <combination-select :search_params="search_params"
                @ready="onCombinationReady" >
            </combination-select>
        </div>
    </div>
    <list v-if="course_id" :course_id="course_id"></list>
   
    
   
</div>

</template>

<script>
    
    import List from '../../components/student/list.vue'
    export default {
        name: 'StudentIndex', 
        components: {
             List
        },
        data() {
            return {
               
               title:Helper.getIcon('students') + ' 學員管理',
                
               search_params:{
                    term:0,
                    center:0,
                 
                    reviewed:1
                },

                course_id:0,

               
               err_msg:''
             
            }
        },
        computed: {
            
        },
        mounted() {
             this.init()
        },
        methods: {
            init(){
              
               
               
             
            },
            onCombinationReady(course){
               
                this.setCourse(course)
                this.ready=true
            },
            setCourse(val){
                this.course_id=val
            },
            fetchData(){
              
                let url=Helper.buildQuery('/signups-report',this.params)
                axios.get(url)
                .then(response => {
                   this.courses=response.data.courses
                })
                .catch(error=> {
                   Helper.BusEmitError(error)
                })
            },
           
            getStyle(course){
               if(Helper.isTrue(course.underMin)) return 'under-min'
               return ''
            },
            onParamChanged(){
                this.fetchData()
            },
            exportReport(){
                document.forms['form-export'].submit()
            },
            
            
            
        },

    }
</script>

