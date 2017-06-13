<template>
    <div>
    <data-viewer v-if="loaded" :defaultSearch="defaultSearch" :defaultOrder="defaultOrder"
     :source="source" :searchParams="searchParams" :thead="thead" 
    :filter="filter" :create="create" :title="title" @refresh="init">
         <div  class="form-inline" slot="header">
              <select  v-model="searchParams.center"  style="width:auto;" class="form-control selectWidth">
                     <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                </select>

         </div>
        
        <template scope="props">
            <tr>    
                <td>
                    <a herf="#" @click="$router.push('/volunteers/' + props.item.id)">
                        {{props.item.profile.fullname}}
                    </a> 
                </td>
                <td v-text="getTitle(props.item.profile)"></td>               
                <td>{{props.item.phone}}</td>
                <td v-html="$options.filters.namesText(props.item.volunteer.centerNames)"></td>
                <td v-text="props.item.volunteer.join_date"></td>    
            </tr>
        </template>

   </data-viewer>

<div>
    </template>
    <script>
        export default {
            name: 'VolunteerIndex',
            components: {
                DataViewer,
            },
            data() {
                return {
                    loaded:false,

                    title: 'Volunteers',
                    source: '/api/volunteers',
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
                        title: '稱謂',
                        key: 'profile.title',
                        sort: false,
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
                    }, {
                         title: '加入日期',
                         key: 'volunteer.join_date',
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
                        center:0
                    }
                    this.loadCenterOptions()
                },
                getTitle(profile){
                    if(profile.title) return profile.title.name
                    return ''
                },
               
                setSearchParams(center){
                    this.searchParams={
                        center:center
                    }

                },
                loadCenterOptions(){
                    let url = '/api/centers/options' 
                     axios.get(url)
                     .then(response => {
                        this.centerOptions = response.data.options 
                        let allCenters={ text:'全部開課中心' , value:'0' }
                        this.centerOptions.splice(0, 0, allCenters);
                        let center=this.centerOptions[0].value
                        this.setSearchParams(center)

                        this.loaded=true
                     })
                     .catch( error=> {
                        console.log(error)                            
                    })
                },
                
            },

           
        }
    </script>