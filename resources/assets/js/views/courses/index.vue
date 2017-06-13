<template>
<div>
   
    
    <data-viewer v-if="loaded" :defaultSearch="defaultSearch" :defaultOrder="defaultOrder"
     :source="source" :searchParams="searchParams" :thead="thead"   :filter="filter" 
     createText="新增課程" :title="title"
     @beginCreate="beginCreate" @refresh="init" @dataLoaded="onDataLoaded">
         
         <div  class="form-inline" slot="header">
               <select  v-model="searchParams.term"   style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in termOptions" :value="item.value" v-text="item.text"></option>
               </select>
               &nbsp;&nbsp;
               <select  v-model="searchParams.center"  style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
               </select>
               &nbsp;&nbsp;
                <select  v-model="searchParams.category" style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in categoryOptions" :value="item.value" v-text="item.text"></option>
                </select>
                &nbsp;&nbsp;
                <select  v-model="searchParams.weekday"  style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in weekdayOptions" :value="item.value" v-text="item.text"></option>
                </select>
                &nbsp;&nbsp;
                <button v-show="hasData" class="btn btn-default btn-xs" @click.prevent="btnViewMoreClicked">
                <span v-if="viewMore" class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                  <span v-if="!viewMore" class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
                </button>
         </div>
        
         <template scope="props">
            <tr>
                <td v-text="props.item.center.name"></td>   
                <td v-text="props.item.number"></td>   
                <td><a herf="#" @click="$router.push('/courses/' + props.item.id)">{{props.item.name}}</a></td> 
                <td v-if="!viewMore" v-html="teachersText(props.item.teachers)"></td> 
                <td v-if="!viewMore" v-html="getClassTimesText(props.item.class_times)"></td> 
                <td v-if="!viewMore" v-html="period(props.item.begin_date , props.item.end_date)"></td> 
                <td v-if="!viewMore" v-html="period(props.item.open_date , props.item.close_date)"></td> 
                <td v-if="!viewMore" v-html="$options.filters.activeLabel(props.item.active)" ></td> 

                <td v-if="viewMore" v-text="props.item.credit_count"></td>
                <td v-if="viewMore" v-text="props.item.weeks"></td>
                <td v-if="viewMore" v-text="props.item.hours"></td>
                <td v-if="viewMore">{{ props.item.tuition | formatMoney }}</td>
                <td v-if="viewMore" v-text="props.item.materials">
                <td v-if="viewMore">{{ props.item.cost | formatMoney }}</td>
                <td v-if="viewMore" v-text="props.item.limit"></td>
                <td v-if="viewMore" v-text="props.item.min"></td>
            </tr>
        </template>

   </data-viewer>

<div>
</template>


<script>
    export default {
        name: 'CourseIndex',
        components: {
            DataViewer,              
        },
        data() {
            return {
                loaded:false,

                title: 'Courses',
                source: '/api/courses',
                
                defaultSearch:'name',
                defaultOrder:'begin_date',                
                create: '/courses/create',
                
                thead:[],
                filter: [{
                    title: '名稱',
                    key: 'name',
                },{
                    title: '開始日期',
                    key: 'begin_date',
                }],
                

                termOptions:[],
                centerOptions:[],
                categoryOptions:[],
                weekdayOptions:[],
                searchParams:{},
                hasData:false,
                viewMore:false
               
            }
        },
        beforeMount() {
             this.init()
        },
        
        methods: {
            init(){
                this.loaded=false
                this.thead=CourseScripts.getThead()
                this.searchParams={
                    term:0,
                    center:0,
                    category:0,
                    weekday:0
                }
                

                let options = this.loadOptions()
                options.then(() => {
                   this.loaded=true
                });
             
            },
            beginCreate(){
                this.$router.push('/courses/create')  
            },
            onDataLoaded(data){
                this.hasData=data.model.total
            },
            
            teachersText(teachers){
                 return CourseScripts.teachersText(teachers)             
            },
            getClassTimesText(class_times){
                return CourseScripts.getClassTimesText(class_times)             
            },
            displayUp(id){
               this.updateDisplayOrder(id,true)
            },
            displayDown(id){
                 this.updateDisplayOrder(id,false)
            },
            updateDisplayOrder(id,up){
                let form = new Form({                        
                     up: up
                })
                
                let url = '/api/centers/' + id + '/displayOrder'

                form.put(url)
                .then(center => {
                   this.version+=1
                })
                .catch(error => {
                    BusEmitError(error,'更新排序失敗')
                })
            },
            loadOptions(){
                return new Promise((resolve, reject) => {
                     let url = '/api/courses/indexOptions' 
                     axios.get(url)
                    .then(response => {
                        this.termOptions = response.data.termOptions
                        let allTerms={ text:'所有學期' , value:'0' }
                        this.termOptions.splice(0, 0, allTerms);
                        let term=this.termOptions[0].value
                        this.searchParams.term=term

                        this.centerOptions = response.data.centerOptions
                        let allCenters={ text:'全部開課中心' , value:'0' }
                        this.centerOptions.splice(0, 0, allCenters);
                        let center=this.centerOptions[0].value
                        this.searchParams.center=center


                        this.categoryOptions = response.data.categoryOptions
                        let defaultItem={ text:'全部課程分類' , value:'0' }
                        this.categoryOptions.splice(0, 0, defaultItem);
                        let category=this.categoryOptions[0].value
                        this.searchParams.category=category 

                        this.weekdayOptions = response.data.weekdayOptions
                        let allWeekdays={ text:'不限星期' , value:'0' }
                        this.weekdayOptions.splice(0, 0, allWeekdays);
                        let weekday=this.weekdayOptions[0].value
                        this.searchParams.weekday=weekday

                        resolve(true);
                    })
                    .catch(error => {
                        reject(error.response);
                    })
                })   //End Promise
            },
           
            btnViewMoreClicked(){
                this.viewMore=!this.viewMore
                for (var i = this.thead.length - 1; i >= 0; i--) {
                    if(!this.thead[i].static){
                        this.thead[i].default = !this.thead[i].default
                    }
                    
                }
            },
            period(begin,end){
               return Helper.period(begin,end)
            }
        },

        
    }
</script>