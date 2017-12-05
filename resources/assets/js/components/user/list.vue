<template>
<data-viewer :source="source" :thead="thead" :filter="filter"  :title="title" 
    :default_order="defaultOrder" :default_search="defaultSearch" :version="version"
    :create_text="createText"   @beginCreate="beginCreate" >
     
       
    <template scope="props">
        <tr>    
            
            <td><a href="#" @click.prevent="userSelected(props.item.id)">{{props.item.name}}</a> </td>
            
            <td>{{props.item.email}}</td>
            <td>{{props.item.phone}}</td>
            <td>
                    
                <role-label v-for="(role,index) in props.item.roles" :key="index" :role="role" ></role-label>
            </td>
            
            <td>{{ props.item.updated_at | tpeTime }}</td>    
        </tr>
    </template>

</data-viewer>
</template>

<script>
    import UserRoles from '../../components/user/user-roles.vue'
    export default {
        name: 'UserList',
        props: {
            hide_create: {
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            }
        },
        components: {
            'user-roles':UserRoles
        },
        data() {
            return {
                title:Helper.getIcon('Users')  + '  使用者管理',
                defaultOrder:'updated_at',
                defaultSearch:'profile.fullname',
                
                createText:'新增使用者',
                
                source: User.source(),
                thead: [{
                    title: '使用者名稱',
                    key: 'name',
                    default:true,
                    sort: true
                }, {
                    title: 'Email',
                    key: 'email',
                    default:true,
                    sort: true
                }, {
                    title: '手機',
                    key: 'phone',
                    default:true,
                    sort: true

                }, {
                    title: '角色',
                    key: 'roles',
                    default:true,
                    sort: false
                }, {
                    title: '更新時間',
                    default:true,
                    key: 'updated_at',
                    sort: true
                }],


                filter: [{
                    title: '真實姓名',
                    key: 'profile.fullname',
                },{
                    title: '使用者名稱',
                    key: 'name',
                }, {
                    title: 'Email',
                    key: 'email',
                }, {
                    title: '手機',
                    key: 'phone',
                }, {
                    title: '更新時間',
                    key: 'updated_at',
                }],

               

                  userRoles:{
                    show:false,
                    adding:true,
                },   
             
            }
        },
        beforeMount() {
           this.init()
        },
        methods: {
            init() {
                
                if(this.hide_create) this.createText=''
            },
            
            userSelected(id){
                this.$emit('selected',id)
            },
            
            beginCreate(){
                 this.$emit('begin-create')
            },   
        },

    }
</script>