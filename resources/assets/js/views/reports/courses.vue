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
            <div>
                <button v-show="hasData" @click.prevent="exportReport" class="btn btn-warning btn-sm">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> 匯出報表
                </button>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
             <div class="panel-title">
                 <h4 v-html="title">
                 </h4>
             </div>
           
             <div  class="form-inline" slot="header">               
                <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="onBtnViewMoreClicked">
                    <span v-if="courseRowSettings.viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                    <span v-if="!courseRowSettings.viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
                </button>
             </div>
        </div>
        <div class="panel-body">
           
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th v-for="item in thead" v-if="item.default" v-bind:style="{ width: item.width }" v-text="item.title">
                        </th>
                    </tr>
                    
                </thead>
                <tbody>
                    <row v-for="course in courses" :course="course" :more="courseRowSettings.viewMore" 
                       :select="courseRowSettings.can_select" >
                      
                        
                    </row>                   
                </tbody>
            </table>
        </div>
       
    </div>
    
    <form id="form-export" action="/courses-report" method="post">
        <input name="term" type="hidden" :value="params.term"  >
        <input name="center" type="hidden" :value="params.center"  >
    </form>
</div>

</template>

<script>
    import Row from '../../components/course/row.vue'

    export default {
        name: 'CoursesReport',       
        components: {
             Row
        },
        data() {
            return {
               
                title:Helper.getIcon('Courses') + ' 課程清單',
                thead:Course.getThead(),

                termOptions:[],
                centerOptions:[],
                params:{
                    term:0,
                    center:0,
                 
                },

                courses:[],

                courseRowSettings:{
                    can_select:false,
                    viewMore:false,
                },

                courseIds:'hhh'

             
            }
        },
        computed: {
            hasData () {
                return this.courses.length > 0
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

                    this.fetchData()
                }).catch(error=>{
                    Helper.BusEmitError(error)                   
                })
             
            },
            fetchData(){
                let url=Helper.buildQuery('/courses-report',this.params)
                axios.get(url)
                .then(response => {
                   this.courses=response.data.courseList
                })
                .catch(error=> {
                   Helper.BusEmitError(error)
                })
            },
            onBtnViewMoreClicked(){
                this.courseRowSettings.viewMore=!this.courseRowSettings.viewMore
                for (var i = this.thead.length - 1; i >= 0; i--) {
                    if(!this.thead[i].static){
                        this.thead[i].default = !this.thead[i].default
                    }
                    
                }
            },
            // setCourseIds(){
            //     return new Promise((resolve, reject) => {
            //         this.courseIds=''
            //          for (let i = 0; i < this.courses.length; i++) {
            //                this.courseIds += String(this.courses[i].id)
            //                if(i < this.courses.length-1){
            //                     this.courseIds += ','
            //              }
            //         }
            //         resolve(true)
            //     })
            // },
            exportReport(){
                document.forms['form-export'].submit()
            }
            
            
        },

    }
</script>