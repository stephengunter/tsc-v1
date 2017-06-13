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
                <td><a herf="#" @click="selected(props.item.user_id)">{{props.item.user.profile.fullname}}</a> </td>
                <td>{{props.item.user.profile.titleText}}</td>
                <td>{{props.item.user.phone}}</td>
                <td v-html="$options.filters.namesText(props.item.centerNames)"></td>
                <td v-html="$options.filters.activeLabel(props.item.active)" ></td>
                <td v-text="props.item.join_date"></td>  
            </tr>
        </template>

    </data-viewer>

</template>

<script>
     
    export default {
        name: 'VolunteerList',
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
        },
        beforeMount() {
           this.init()
        },
        data() {
            return {
                title:Helper.getIcon('Volunteers')  + '  志工管理',
                loaded:false,
                source: Volunteer.source(),
                
                defaultSearch:'user.profile.fullname',
                defaultOrder:'join_date',                
                create: Volunteer.createUrl(),
                
                thead:[],
                filter: [
                         {
                            title: '姓名',
                            key: 'user.profile.fullname',
                         }
                        ],

                centerOptions:[],
                searchParams:{
                    center : 0,
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
               
                this.thead=Volunteer.getThead(this.can_select)

                this.searchParams={
                    center : 0,
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
            
            selected(id){
                this.$emit('selected',id)
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },
            
           
        },

    }
</script>