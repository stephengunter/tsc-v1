<template>
    <div>
    <data-viewer v-if="loaded" :defaultSearch="defaultSearch" :defaultOrder="defaultOrder"
     :source="source" :searchParams="searchParams" :thead="thead" 
    :filter="filter" :create="create" :title="title" @refresh="init">
        <div  class="form-inline" slot="header">
            <select  v-model="searchParams.center" style="width:auto;" class="form-control selectWidth">
                <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
            </select>
            &nbsp;&nbsp;
            <select  v-model="searchParams.role"   style="width:auto;" class="form-control selectWidth">
                <option v-for="item in roleOptions" :value="item.value" v-text="item.text"></option>
            </select>

        </div>
        
        <template scope="props">
            <tr>    
                <td>
                    <a herf="#" @click="$router.push('/admins/' + props.item.id)">
                        {{props.item.profile.fullname}}
                    </a> 
                </td>
                <td>
                    <role-label :labelstyle="props.item.admin.roleModel.style" 
                        :labeltext="props.item.admin.roleModel.display_name">                        
                    </role-label>
                </td>               
                <td v-text="props.item.phone"></td>
                <td v-html="getCenterNames(props.item.admin.centers)"></td> 
               
            </tr>
        </template>

   </data-viewer>

<div>
</template>
<script>
    import RoleLabel from '../../components/RoleLabel.vue'

    export default {
        name: 'AdminIndex',
        components: {
            DataViewer,
            RoleLabel
        },
        data() {
            return {
                loaded:false,

                title: 'Admins',
                source: '/api/admins',
                create: false,
                defaultSearch:'profile.fullname',
                defaultOrder:'updated_at',
                thead: [{
                    title: '姓名',
                    key: 'profile.fullname',
                    sort: false,
                    default:true,
                    width:'20%'
                },{
                    title: '角色',
                    key: 'admin.role',
                    sort: true,
                    default:true,
                    width:'10%'
                }, {
                    title: '手機',
                    key: 'phone',
                    default:true,
                    sort: true
                   
                }, {
                     title: '所屬中心',
                     key: 'centers',
                     default:true,
                     sort: false
                }],


                filter: [{
                    title: '姓名',
                    key: 'profile.fullname',
                }],

                centerOptions:[],
                searchParams:{},
           }
            
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init(){
                
                this.loaded=false
                this.searchParams={
                    center:0,
                    role:'',
                }

                let options = this.loadOptions()
                options.then(() => {
                   this.loaded=true
                }) 
            },
            getTitle(profile){
                if(profile.title) return profile.title.name
                return ''
            },
            
            loadOptions(){
                return new Promise((resolve, reject) => {
                    let url = '/api/admins/indexOptions' 
                    axios.get(url)
                    .then(response => {

                        this.centerOptions = response.data.centerOptions
                        let allCenters={ text:'全部開課中心' , value:'0' }
                        this.centerOptions.splice(0, 0, allCenters);
                        let center=this.centerOptions[0].value
                        this.searchParams.center=center

                        this.roleOptions = response.data.roleOptions
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
            tpeTime(datetime) {
                return Helper.tpeTime(datetime)
            },
            getCenterNames(centers){
                if(centers.length){

                    let names=''
                   for (var i =0 ; i < centers.length; i++) {
                       names +=centers[i].name + '&nbsp;&nbsp;'
                   }

                   return names
                }
            }
        },

       
    }
</script>