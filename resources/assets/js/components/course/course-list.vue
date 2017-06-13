<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
             <span  class="panel-title">
                 <span  class="panel-title">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> 課程管理
                  </span>
             </span>
             <div class="form-inline">
                <select  v-model="searchParams.category" style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in categoryOptions" :value="item.value" v-text="item.text"></option>
                </select>
                
                &nbsp;&nbsp;
                <select  v-model="searchParams.term" @change="fetchData"  style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in termOptions" :value="item.value" v-text="item.text"></option>
                </select>
                
               
                &nbsp;&nbsp;
                 <select  v-model="searchParams.active" @change="fetchData"  style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in statusOptions" :value="item.value" v-text="item.text"></option>
                </select>
                 &nbsp;&nbsp;
                 <select  v-model="searchParams.center" @change="fetchData"  style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                </select>

                  &nbsp;&nbsp;
                <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="btnViewMoreClicked">
                <span v-if="viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                  <span v-if="!viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
                </button>
             </div>
           
             <div>   
               <button  @click="beginAddCourse" class="btn btn-primary btn-sm" >
                  <span class="glyphicon glyphicon-plus"></span> 新增
               </button>
                
            </div>
        </div>
        <div class="panel-body" v-if="hasData">
          <course-table :courses="courseList" :remove="true" :more="viewMore"
            @removeClicked="btnDeleteClicked">
          </course-table>
           
        </div>
        
    </div>

     <modal :showBtn="true"  :show.sync="showConfirm" @ok="removeCourse"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
          <div slot="modal-header" class="modal-header modal-header-danger">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="closeConfirm">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
             <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <h3 v-text="confirmMsg"> </h3>
        </div>
    </modal>
    
</div>
</template>
<script>
    import CourseTable from '../../components/course/course-table.vue'
    export default {
        props: ['category','version'],
        components: {
            CourseTable,
            Modal
        },
        name: 'CourseList',
        data() {
            return {
                searchParams:{
                    term:0,
                    category:0,
                    center:0,
                    active:1
                },
                
                categoryOptions:[],
                centerOptions:[],
                termOptions:[],
                courseList:[],

                viewMore:false,

                deleteId:0,
                confirmMsg:'',
                showConfirm:false,

            }
        },
        watch: {
            'version': 'init'
        },
        computed:{
            hasData(){
                if(this.courseList.length) return true
                return false    
            }
        },      
        beforeMount() {
            this.init();
            
        },
        methods: {
            init(){

                this.confirmMsg='',
                this.deleteId=0,
                this.showConfirm=false
               
                this.categoryOptions=[
                    {
                        text:this.category.name,
                        value:this.category.id
                    }
                ]
                this.statusOptions=[
                    {
                        text:'上架中',
                        value:1
                    }
                ]
               
                this.courseList= []
                this.searchParams={
                    term:0,
                    category:this.category.id,
                    center:0,
                    active:1
                }
                

                let options = this.loadOptions()
                options.then(() => {
                    this.fetchData()
                }).catch(error => {
                    console.log(error)
                })
              
              
            },
            
            loadOptions(){
                return new Promise((resolve, reject) => {
                     let url = '/api/category-course/index-options' 
                     axios.get(url)
                    .then(response => {
                        this.termOptions = response.data.termOptions
                        let term=this.termOptions[0].value
                        this.searchParams.term=term

                        this.centerOptions = response.data.centerOptions
                        let allCenters={ text:'全部開課中心' , value:'0' }
                        this.centerOptions.splice(0, 0, allCenters);
                        let center=this.centerOptions[0].value
                        this.searchParams.center=center
                       

                        resolve(true);
                    })
                    .catch(error => {
                        reject(error);
                    })
                })   //End Promise
            },
            fetchData() {
               let url= Helper.buildQuery('/api/category-course',this.searchParams)
              
               axios.get(url)
                    .then(response => {
                       this.courseList=response.data.courseList
                       this.loaded = true
                        
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
                
            },
           
            btnViewMoreClicked(){
                this.viewMore=!this.viewMore
            },
            beginAddCourse(){
                let term=this.searchParams.term
                let center=this.searchParams.center
                this.$emit('addCourse',center,term)
            },
            
            btnDeleteClicked(values){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要移除 ' + values.name + ' 與此分類的關聯嗎？'
                    this.deleteId=values.id
                    this.showConfirm=true
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
                
            },
            removeCourse(id){
                let url = '/api/category-course/remove' 
              
                 let form=new Form({
                    category:this.category.id,
                    course:this.deleteId
                })
                form.post(url)
                .then(result => {
                    this.init()
                    Helper.BusEmitOK('移除成功') 

                    this.deleteId=0;
                    this.closeConfirm();

                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'移除失敗')
                    this.deleteId=0;
                    this.closeConfirm();
                       
                })
            },
            closeConfirm(){
                this.showConfirm=false
            },
            
            
        },
        
    }
</script>