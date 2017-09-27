<template>

    <div class="panel panel-default">
        <div class="panel-heading">
             <div class="panel-title">
                 <h4 v-html="title">
                 </h4>
             </div>
              

            <button v-show="!hide_create" class="btn btn-primary btn-sm" @click="onAddClicked">
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
                title:Helper.getIcon('Teachers')  + '  群組教師',
                loaded:false,
                
                thead:Teacher.getThead(),

                teachers:[],
             
            }
        },
        computed: {
            
        },
        methods: {
            init() {
                this.loaded=false
                 
                
            },
           
            
            onAddClicked(){
                 this.$emit('begin-create')
            },
            
           
        },

    }
</script>