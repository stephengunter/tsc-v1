<template>
    <data-viewer  :default_search="defaultSearch" :default_order="defaultOrder"
       :source="source"   :thead="thead"  
       :filter="filter"  :title="title" :create_text="createText" 
       @refresh="init" :version="version"   @beginCreate="beginCreate" >
       
     
         
         
         <template scope="props">
            <tr>
               
                <td><a href="#" @click.prevent="selected(props.item.user_id)">{{props.item.user.profile.fullname}}</a> </td>
                <td>{{ props.item.user.profile.title }}</td>
                <td>{{ props.item.user.phone }}</td>
                
                <td v-text="props.item.user.email"></td>  
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
                
                thead: [{
                    title: '姓名',
                    key: 'name',
                    sort: false,
                    default: true
                }, {
                    title: '稱謂',
                    key: 'user.profile.titleText',
                    sort: false,
                    default: true
                }, {
                    title: '手機',
                    key: 'user.phone',
                    sort: false,
                    default: true
                },{
                    title: 'Email',
                    key: 'user.email',
                    sort: false,
                    default: true
                }],
                filter: [
                    {
                        title: '姓名',
                        key: 'user.profile.fullname',
                    }
                 ],

                centerOptions:[],
                
              
                viewMore:false
             
            }
        },
        computed: {
            createText(){
                if(this.hide_create) return ''
                return '新增志工'
            },
        },
        methods: {
            init() {
               
                
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