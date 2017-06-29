<template>
    <data-viewer v-if="loaded"  :default_search="defaultSearch" :default_order="defaultOrder"
      :source="source" :search_params="searchParams"  :thead="thead" :no_search="can_select"  
      :filter="filter"  :title="title" :create_text="createText" 
      @refresh="init" :version="version"   @beginCreate="beginCreate"
       @dataLoaded="onDataLoaded">
     
         
         <div class="form-inline"   slot="header">
            <select  v-model="searchParams.center" style="width:auto;" class="form-control selectWidth">
                <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
            </select>
            &nbsp;&nbsp;
            <select  v-model="searchParams.role"    style="width:auto;" class="form-control selectWidth">
                <option v-for="item in roleOptions" :value="item.value" v-text="item.text"></option>
            </select>

        </div>
         <template scope="props">
            <tr>
                <td><a herf="#" @click="selected(props.item.id)">{{props.item.profile.fullname}}</a> </td>
                <td>
                    <role-label :labelstyle="props.item.admin.roleModel.style" 
                        :labeltext="props.item.admin.roleModel.display_name">                        
                    </role-label>
                </td>               
                <td v-text="props.item.phone"></td>
                <td v-html="getCenterNames(props.item.admin.centers)"></td> 
                <td v-html="$options.filters.activeLabel(props.item.active)" ></td>
                <td>
                    <updated :entity="props.item"></updated>
                </td>
            </tr>
        </template>

    </data-viewer>

</template>

<script>
     
    export default {
        name: 'AdminList',
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
                title:Helper.getIcon('Admins')  + '  系統管理員',
                loaded:false,
                source: Admin.source(),
                
                defaultSearch:'profile.fullname',
                defaultOrder:'updated_at',                
                create: Admin.createUrl(),
                
                thead:[],
                filter: [
                         {
                            title: '姓名',
                            key: 'profile.fullname',
                         }
                        ],

                centerOptions:[],
                roleOptions:[],
                searchParams:{
                    center : 0,
                    role:''
                },
              
              
                viewMore:false
             
            }
        },
        computed: {
            createText(){
                if(this.hide_create) return ''
                return '新增管理員'
            },
        },
        methods: {
            init() {
                this.loaded=false
               
                this.thead=Admin.getThead(this.can_select)

                this.searchParams={
                    center : 0,
                    role:''
                }
                let options = this.loadOptions()
                options.then(() => {
                          this.loaded=true
                       }).catch( error=> {
                          Helper.BusEmitError(error)           
                       })
            },
            loadOptions(){
                return new Promise((resolve, reject) => {
                    let options = Admin.indexOptions()
                  
                    options.then(data => {

                        this.centerOptions = data.centerOptions
                        let allCenters={ text:'全部開課中心' , value:'0' }
                        this.centerOptions.splice(0, 0, allCenters);
                        let center=this.centerOptions[0].value
                        this.searchParams.center=center

                        this.roleOptions = data.roleOptions
                        let allRoles={ text:'所有角色' , value:'' }
                        this.roleOptions.splice(0, 0, allRoles);
                        let role=this.roleOptions[0].value
                        this.searchParams.role=role

                        resolve(true);
                    })
                    .catch(error => {
                        reject(error.response);
                    })
                })//End Promise
            },
            getCenterNames(centers){
                if(centers.length){

                    let names=''
                   for (var i =0 ; i < centers.length; i++) {
                       names +=centers[i].name + '&nbsp;&nbsp;'
                   }

                   return names
                }
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