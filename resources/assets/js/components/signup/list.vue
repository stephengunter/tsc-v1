<template>
    <data-viewer v-if="loaded"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
     
         <div v-if="course_id"  class="form-inline" slot="header">
             <span v-if="summary" >
              總數：{{ summary.total }} 筆 &nbsp; 已繳費：{{ summary.success }} 筆 &nbsp; 待繳費：{{ summary.default }} 筆 &nbsp; 已取消：{{ summary.canceled }} 筆
              &nbsp;&nbsp;&nbsp;&nbsp;
              </span>
              <select v-model="searchParams.status"  style="width:auto;" class="form-control selectWidth">
                  <option v-for="item in statusOptions" :value="item.value" v-text="item.text"></option>
              </select>
               
         </div>
         <template scope="props">
            <tr>
                <td v-if="can_select">
                    <button @click.prevent="selected(props.item.id)"  type="button" class="btn-xs btn btn-primary">
                        選取
                    </button>
                </td> 
                
                <td v-text="props.item.user.profile.fullname"></td> 
                <td v-text="props.item.date"></td> 
                <td>
                  <span v-if="isTrue(props.item.net_signup)" class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                </td> 
                <td>
                   <button @click.prevent="selected(props.item.id)" type="button" :class="statusStyle(props.item.status)">
                   {{ statusText(props.item.status) }}
                   </button>
                </td>
                <td v-html="getFormatedCourseName(props.item.course)"></td>    
                <td>{{ props.item.tuition | formatMoney }}</td>  
                <td v-html="discountText(props.item)"></td>
            </tr>
        </template>

    </data-viewer>

</template>

<script>
     
    export default {
        name: 'SignupList',
        props: {
            course_id: {
              type: Number,
              default: 0
            },
            user_id: {
              type: Number,
              default: 0
            },
            hide_create: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            },
            can_select:{
               type: Boolean,
               default: true
            },
        },
        beforeMount() {
           this.init()
        },
        watch: {
            course_id: function (value) {
               this.searchParams.course=value
            }
        },
        data() {
            return {
                title:Helper.getIcon('Signups')  + '  報名紀錄',
                loaded:false,
                source: Signup.source(),
                
                defaultSearch:'date',
                defaultOrder:'date',                
                create: Signup.createUrl(),
                
                thead:[],
                filter: [{
                    title: '報名日期',
                    key: 'date',
                }],

                summary:null,

                statusOptions:[],
                searchParams:{
                    user : this.user_id,
                    course : this.course_id,
                    status : 0
                },
                hasData:false,
                viewMore:false
             
            }
        },
        computed: {
            createText(){
                if(this.hide_create) return ''
                return '新增報名'
            },
        },
        methods: {
            init() {
                this.loaded=false
               
                this.thead=Signup.getThead(this.can_select)

                if(this.course_id){
                    let options = this.loadStatusOptions()
                    options.then((value) => {
                        this.searchParams={
                            course : this.course_id,
                            status : value
                        }

                       this.loaded=true
                    })
                }else{
                    this.searchParams={
                            user : this.user_id,
                        }
                    this.loaded=true
                }
                
            },
            loadStatusOptions(){
                 return new Promise((resolve, reject) => {
                    let options=Signup.statusOptions()
                    options.then(data => {
                        this.statusOptions = data.options
                        let allStatuses={ text:'總數' , value:'-9' }
                        this.statusOptions.splice(0, 0, allStatuses);
                        resolve(this.statusOptions[0].value);
                    })
                    .catch(error => {
                        console.log(error)
                        reject(error.response);
                    })
                })   //End Promise
            },
            onDataLoaded(data){
                if(data.summary)  this.summary=data.summary
                else this.summary=null
            }, 
            isTrue(val){
               return Helper.isTrue(val)
            },
            statusStyle(status){
                return 'btn-xs btn btn-' + Signup.getStatusStyle(status)
            },
            statusText(status){
                return Signup.getStatusText(status)
            },
           
            getFormatedCourseName(course){
                return Signup.getFormatedCourseName(course)
            },
            discountText(signup){
                if(!signup.discount) return ''
                return Signup.formatDiscountText(signup.discount, signup.points)
            },
            selected(id){
                this.$emit('selected',id)
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            
           
        },

    }
</script>