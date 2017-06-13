<template>
    <data-viewer v-if="loaded"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
     
         <div  class="form-inline" slot="header">
             <span v-if="summary">
                  總數：{{ summary.total }} 筆 &nbsp; 待審核：{{ summary.default }} 筆 &nbsp; 審核中：{{ summary.processing }} 筆 &nbsp; 已完成：{{ summary.success }} 筆
             </span>
                  &nbsp;&nbsp;&nbsp;&nbsp;
             <select v-model="searchParams.status"  style="width:auto;" class="form-control selectWidth">
                  <option v-for="item in statusOptions" :value="item.value" v-text="item.text"></option>
             </select>
               
         </div>
         <template scope="props">
              <tr>
                  <td v-if="can_select">
                    <button @click.prevent="selected(props.item.signup_id)"  type="button" class="btn-xs btn btn-primary">
                        選取
                    </button>
                  </td>
                  <td v-text="props.item.number"></td>
                  <td v-text="props.item.date"></td> 
                  <td>
                      <button @click.prevent="selected(props.item.signup_id)" type="button" :class="statusStyle(props.item.status)">
                         {{ statusText(props.item.status) }}
                      </button>
                  </td>
                  <td v-text="props.item.signup.user.profile.fullname"></td> 
                  <td v-html="getFormatedCourseName(props.item.signup.course)"></td> 
                  <td>{{ props.item.total | formatMoney }}</td>  
                  <td v-text="props.item.textPayBy"></td>
              </tr>
         </template>

    </data-viewer>

</template>

<script>
     
    export default {
        name: 'RefundList',
        props: {
            term_id: {
              type: Number,
              default: 0
            },
            center_id: {
              type: Number,
              default: 0
            },
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
            term_id: function (value) {
               this.searchParams.term=value
            },
            center_id: function (value) {
               this.searchParams.center=value
            },
            course_id: function (value) {
               this.searchParams.course=value
            },
        },
        data() {
            return {
                title:Helper.getIcon('Refunds')  + '  退費申請',
                loaded:false,
                source: Refund.source(),
                
                defaultSearch:'date',
                defaultOrder:'date',                
                create: Refund.createUrl(),
                
                thead:[],
                filter: [{
                    title: '申請日期',
                    key: 'date',
                }],

                summary:null,

                statusOptions:[],
                searchParams:{
                    term :0,
                    center : 0,
                    course : 0,
                    status : 0
                },
                hasData:false,
                viewMore:false
             
            }
        },
        computed: {
            createText(){
                if(this.hide_create) return ''
                return '新增退費申請'
            },
        },
        methods: {
            init() {
                this.loaded=false
               
                this.thead=Refund.getThead(this.can_select)

                let options = this.loadStatusOptions()
                options.then((value) => {
                    this.searchParams.status=value

                   this.loaded=true
                }).catch(error => {
                     Helper.BusEmitError(error)  
                })
                
            },
            loadStatusOptions(){
                 return new Promise((resolve, reject) => {
                    let options=Refund.statusOptions()
                    options.then(data => {
                        this.statusOptions = data.options
                        resolve(this.statusOptions[0].value);
                    })
                    .catch(error => {
                        reject(error.response);
                    })
                })   //End Promise
            },
            onDataLoaded(data){
                if(data.summary)  this.summary=data.summary
                else this.summary=null
            }, 
            statusStyle(status){
                return 'btn-xs btn btn-' + Refund.getStatusStyle(status)
            },
            statusText(status){
                return Refund.getStatusText(status)
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