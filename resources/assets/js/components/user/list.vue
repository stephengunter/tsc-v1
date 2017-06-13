<template>
<div>
<data-viewer :source="source" :thead="thead" :filter="filter"  :title="title" 
    :default_order="defaultOrder" :default_search="defaultSearch" :version="version"
     :create_text="createText" 
      @beginCreate="beginCreate" >
       
        <template scope="props">
            <tr>    
               
                <td><a herf="#" @click.prevent="userSelected(props.item.id)">{{props.item.name}}</a> </td>
                
                <td>{{props.item.email}}</td>
                <td>{{props.item.phone}}</td>
                <td>
                      <button type="button" class="btn btn-default btn-xs" @click="addUserToRole(props.item.id)">
                          <span class="glyphicon glyphicon-plus"></span>
                      </button>
                      
                    <role-label v-for="role in props.item.roles" :labelstyle="role.style" :labeltext="role.display_name"></role-label>
                </td>
               
                <td>{{ props.item.updated_at | tpeTime }}</td>    
            </tr>
        </template>

</data-viewer>

<modal effect="fade" width="800"title="加入角色" :show.sync="showModal" 
    @closed="closeModel"  @ok="submitRole">
    <div slot="modal-body" class="modal-body">
        <p style="font-size:17px">請選擇您要加入的角色</p>
        <toggle v-if="showModal" :items="rolesCanAdd"   :default_val="roleToAdd" @selected=addToRole></toggle>
        
    </div>
</modal>

</div>
</template>

<script>
     
    export default {
        name: 'UserList',
        props: ['hide_create','version'],
        
        data() {
            return {
                title:Helper.getIcon('Users')  + '  使用者管理',
                defaultOrder:'updated_at',
                defaultSearch:'name',
                
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

               

                showModal: false,
                selected: 0,
                roleToAdd: 0,
                rolesCanAdd: [],    
             
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
            addUserToRole(user_id) {
                this.selected = user_id
                let rolesCanAdd=User.roleCanAdd(user_id)
                rolesCanAdd.then((roles) => {
                    let options = []

                    for (let i = 0; i < roles.length; i++) {

                        let option = {
                            text: roles[i].display_name,
                            value: roles[i].name
                        }

                        options[i] = option
                    }

                    this.rolesCanAdd = options
                    this.roleToAdd = options[0].value
                    this.showModal = true
                 })
               

            },
            closeModel() {
                this.showModal = false
            },
            submitRole() {
                this.$emit('add-to-role',this.selected,this.roleToAdd)                   
            },
            addToRole(name) {
                this.roleToAdd = name
            },
        },

    }
</script>