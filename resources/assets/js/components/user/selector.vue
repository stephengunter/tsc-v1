<template>
     <div class="panel panel-default">
        <div class="panel-heading">
            <span  class="panel-title">
                <span  class="panel-title">
                    <h4 v-html="getTitle()">
                        <i class="fa fa-user" aria-hidden="true"></i> 請選擇使用者
                    </h4>
                </span>
            </span>
            
            
        </div>   <!--panel  heading -->
        <div class="panel-body" v-if="this.users.length">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th> </th>
                        <th>Email</th> 
                        <th>手機</th>    
                        <th>姓名</th> 
                        <th>身分證號</th>
                    </tr>
                </thead>
                <tbody>
                   <tr v-for="user in users">
                        
                        <!-- <td>
                             <checkbox @selected="selected(user.id)" @unselected="unselected(user.id)" ></checkbox>
                        </td> -->
                        <td>
                            <button @click.prevent="selected(user.id)"  type="button" class="btn-xs btn btn-primary">
                               選取
                            </button>
                        </td>
                        <td v-text="user.email"></td>  
                        <td v-text="user.phone"></td>   
                        <td v-text="user.profile.fullname"></td>
                        <td v-text="user.profile.SID"></td>  
                        
                    </tr>                   
                </tbody>
            </table>
           
        </div>
       
    </div>
</template>



<script>
    
    export default {
        name: 'UserSelector',
        props: {
            users:{
               type: Array,
               default: []
            }
        },
        data(){
            return {
               selected_ids:[]     
            }
        },
        methods: {
            getTitle(){
                return User.titleHtml()
            },
            selected(id){
                this.selected_ids.push(id)
                this.$emit('selected',this.selected_ids)
            },
            
        }
     }
</script>