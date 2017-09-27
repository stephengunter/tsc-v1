<template>

    <div class="panel panel-default">
        <div class="panel-heading">
             <div class="panel-title">
                 <h4 v-html="title">
                 </h4>
             </div>
              

            <button v-show="can_edit" class="btn btn-primary btn-sm" @click="onAddClicked">
                 <span  class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                 加入教師
            </button>
             
        </div>
        <div class="panel-body">
            
            <table class="table table-striped">
                <thead>
                    <tr>
                      
                        <th v-for="item in thead" >
                           {{item.title}}
                        </th>
                        <th></th>
                    </tr>
                    
                </thead>
                <tbody>
                   <tr v-for="teacher in teachers">
                      
                        <td><a herf="#" @click="selected(teacher.user_id)">{{teacher.user.profile.fullname}}</a> </td>
                        <td>{{teacher.user.phone}}</td>
                        <td>{{teacher.specialty}}</td>
                        
                        <td v-html="$options.filters.namesText(teacher.centerNames)"></td>
                        <td v-html="$options.filters.activeLabel(teacher.active)" ></td>
                        <td v-html="$options.filters.reviewedLabel(teacher.reviewed)" ></td> 
                        <td>{{ teacher.updated_at | tpeTime}}</td> 
                        <td>
                            <button v-if="can_edit"  class="btn btn-danger btn-xs"
                                 @click.prevent="beginDelete(teacher.user_id,teacher.user.profile.fullname)">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </button>
                        </td>
                    </tr>                    
                </tbody>
            </table>
        </div>
       
    </div>
    
   
</template>


<script>
     
    export default {
        name: 'GroupTeacherList',
        props: {
            group_id:{
              type: Number,
              default: 0
            },
            can_edit: {
              type: Boolean,
              default: false
            },
            version:{
               type: Number,
               default: 0
            },
        },
        
        data() {
            return {
                title:Helper.getIcon('Teachers')  + '  群組教師',
                
                
                thead:Teacher.getThead(),

                teachers:[],

                 deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }   
             
            }
        },
        watch:{
          'version' : 'init'
        },
        beforeMount() {
           this.init()
        },
        computed: {
            
        },
        methods: {
            init() {
               
                this.fetchData()
            },
            fetchData(){
                let getData=GroupTeacher.show(this.group_id)
                    getData.then(data => {
                        this.teachers = data.teachers
                    })
                    .catch(error => {
                        reject(error.response);
                    })
            },     
            beginDelete(id,name){
                let values={
                    id:id,
                    name:name
                }
                 this.$emit('begin-delete',values)                
              
            },  
           
            onAddClicked(){
                 this.$emit('begin-edit')
            },
            
            
           
        },

    }
</script>