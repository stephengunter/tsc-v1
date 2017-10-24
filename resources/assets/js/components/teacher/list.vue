<template>
    <data-viewer v-if="loaded"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
     
         
         <div  class="form-inline" slot="header">
            <select  v-model="searchParams.center"  style="width:auto;" class="form-control selectWidth">
                  <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
            </select>

        </div>
         <template scope="props">
            <tr>
                <td v-if="can_select">
                    <button @click.prevent="selected(props.item.user_id)"  type="button" class="btn-xs btn btn-primary">
                        選取
                    </button>
                </td> 
                <td><a herf="#" @click="selected(props.item.user_id,true)">{{props.item.user.profile.fullname}}</a> </td>
                <td>
                  
                  <span v-if="isGroup(props.item)" v-html="$options.filters.okSign(true)"></span>

                </td>
                <td>{{props.item.user.phone}}</td>
                <td>{{props.item.specialty}}</td>
                
                <td v-html="$options.filters.namesText(props.item.centerNames)"></td>
                <td v-if="false" v-html="$options.filters.activeLabel(props.item.active)" ></td>
                <td v-html="$options.filters.reviewedLabel(props.item.reviewed)" ></td> 
                <td>{{ props.item.updated_at | tpeTime}}</td> 
            </tr>
        </template>

    </data-viewer>

</template>

<script>
     
    export default {
        name: 'TeacherList',
        props: {
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
            group:{
               type: Number,
               default: 0
            }
        },
        beforeMount() {
           this.init()
        },
        data() {
            return {
                title:Helper.getIcon('Teachers')  + '  教師管理',
                loaded:false,
                source: Teacher.source(),
                
                defaultSearch:'specialty',
                defaultOrder:'updated_at',                
                create: Teacher.createUrl(),
                
                thead:[],
                filter: [
                         {
                            title: '姓名',
                            key: 'user.profile.fullname',
                         },{
                            title: '專長',
                            key: 'specialty',
                         }, 
                        ],

                centerOptions:[],
                searchParams:{
                    center : 0,
                    group:0
                },
              
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
               
                this.thead=Teacher.getThead(this.can_select)

                this.searchParams={
                    center : 0,
                    group:this.group
                }
                let options = this.loadCenterOptions()
                options.then((value) => {
                          this.searchParams.center=value
                          this.loaded=true
                       }).catch( error=> {
                          Helper.BusEmitError(error)           
                       })
            },
            loadCenterOptions(){
                 return new Promise((resolve, reject) => {
                    
                    let options=Center.options()
                    options.then(data => {
                        this.centerOptions = data.options
                        let allCenters={ text:'全部開課中心' , value:'0' }
                        this.centerOptions.splice(0, 0, allCenters)
                        resolve(this.centerOptions[0].value);
                    })
                    .catch(error => {
                        reject(error.response);
                    })
                })   //End Promise
            },
            onDataLoaded(data){
               
            }, 
            isGroup(teacher){
                return Helper.isTrue(teacher.group)
            },
            selected(id,isLink){
              if(isLink){
                this.$emit('selected',id,true)
              }else{
                 this.$emit('selected',id)

              }
                
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            
           
        },

    }
</script>