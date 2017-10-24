<template>
    <div class="panel panel-default">
        <div  class="panel-heading">
            <div class="panel-title">
                <h4 v-html="title">
                </h4>
            </div>
            <div  class="form-inline" slot="header">
                <select  v-model="searchParams.center"  style="width:auto;" class="form-control selectWidth">
                      <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                </select>
    
            </div>
            <div>
                <button :disabled="!canSubmit" @click.prevent="submit" class="btn btn-success" >
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 審核通過
                </button>
                
            </div>
        </div>
        <div class="panel-body">
            <table v-if="loaded" class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <checkbox  :default="false"
                                @selected="checkAll"   @unselected="unCheckAll">                             
                            </checkbox>
                        </th>
                        <th v-for="item in thead"> {{ item.title }} </th>
                        
                       
                        <!-- <th v-show="!edittingOrder">
                            順序
                            <button v-show="hasData" @click="beginEditOrder" class="btn btn-primary btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                            </button>
                        </th>
                        <th v-show="edittingOrder">
                            順序
                            <button @click="onSubmitDisplayOrders" class="btn btn-success btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-floppy-disk" ></span>
                            </button>
                            <button @click="cancelEditOrder" class="btn btn-default btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                            </button>
                        </th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="teacher in teacherList">
                        <td>
                            
                            <checkbox :value="teacher.user_id" :default="beenChecked(teacher.user_id)"
                                @selected="onChecked"   @unselected="unChecked">
                             
                            </checkbox>
                        </td> 
                        <td><a herf="#" @click="onSelected(teacher.user_id,true)">{{teacher.user.profile.fullname}}</a> </td>
                        <td>
                          
                          <span v-if="isGroup(teacher)" v-html="$options.filters.okSign(true)"></span>
        
                        </td>
                        <td>{{teacher.user.phone}}</td>
                        <td>{{teacher.specialty}}</td>
                        
                        <td v-html="$options.filters.namesText(teacher.centerNames)"></td>
                        <td v-if="false" v-html="$options.filters.activeLabel(teacher.active)" ></td>
                        <td v-html="$options.filters.reviewedLabel(teacher.reviewed)" ></td> 
                        <td>{{ teacher.updated_at | tpeTime}}</td> 
                    </tr>
	                    
                   
                </tbody>  
            </table>
        </div>
    </div>
    
</template>
    
<script>
    export default {
        name: 'TeacherReview',  
        props: {
            version: {
                type: Number,
                default: 0
            },
            
        },
        data() {
            return {
                loaded:false,
                title:Helper.getIcon('Teachers')  + '  教師審核',
                thead:Teacher.getThead(),
                searchParams:{
                    center : 0,
                   
                },

                checked_ids:[],

                teacherList:[],
                centerOptions:[],

                isCheckAll:false
                
            }
        },
        computed: {
            canSubmit() {
               return  this.checked_ids.length > 0
            },
            
        },
        watch: {
          version() {
             this.init()
          },
        
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init(){
                this.loaded=false
                this.checked_ids=[]
                this.teacherList=[]
                this.isCheckAll=false
                this.fetchData()                
            },
            fetchData(){
                let center=this.searchParams.center
                let getData = Teacher.unreviewedList(center)
                getData.then(data => {
                          
                          this.teacherList=data.teacherList
                          if(data.centerOptions.length){
                              this.centerOptions=data.centerOptions
                              this.searchParams.center=this.centerOptions[0].value
                          }
                          
                          this.loaded=true
                       }).catch( error => {
                          Helper.BusEmitError(error)           
                       })
            },
            isGroup(teacher){
                return Helper.isTrue(teacher.group)
            },
            beenChecked(id){
                return this.checked_ids.includes(id)
            },
            onChecked(id){
                if(!this.beenChecked(id))  this.checked_ids.push(id) 
            },
            unChecked(id){
                let index= this.checked_ids.indexOf(id)
                if(index >= 0)  this.checked_ids.splice(index, 1) 
                
            },
            checkAll(){
                if(this.teacherList.length)
                {
                    for(let i=0; i<this.teacherList.length ; i++){
                        this.onChecked(this.teacherList[i].user_id)
                    }
                }
            },
            unCheckAll(){
                this.checked_ids=[]
            },
            onSelected(id){
                
               
            },
            submit(){
                let save = Teacher.updateReviewList(this.checked_ids)
                save.then(data => {
                        this.$emit('saved')
                    }).catch( error => {
                        Helper.BusEmitError(error,'存檔失敗')           
                    })
        }
            
            
        },

    }
</script>