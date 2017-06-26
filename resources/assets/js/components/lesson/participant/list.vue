<template>

<!-- <data-viewer :source="source" :thead="thead" :filter="filter"  :title="title" 
    :default_order="defaultOrder" :default_search="defaultSearch" :version="current_version"
     :create_text="createText" 
      @beginCreate="beginCreate" >
       
        <template scope="props">
            <tr>    
               <td>{{ props.item.number }}</td>
               <td>{{ props.item.user.profile.fullname }}</td>
               <td v-html="getStatusLabel(props.item)"></td>
               <td v-tx="props.item.ps"></td>
               <td>
                   <updated :entity="props.item"></updated>
               </td>  
            </tr>
        </template>

</data-viewer> -->
<div>ccccccccccccc</div>

</template>

<script>
     
    export default {
        name: 'LessonStudents',
        props: {
            lesson_id: {
              type: Number,
              default: 0
            },
            hide_create:{
               type: Boolean,
               default: false
            },
            version:{
               type: Number,
               default: 0
            }
        },
        watch: {
            version: function () {
               this.current_version += 1
            }
        },
        data() {
            return {
                title:Helper.getIcon('students')  + '  學員名單',
                current_version:0,
                defaultOrder:'',
                defaultSearch:'role',
                
                createText:'',
                
                source: LessonParticipant.source(),
                thead: [{
                        title: '',
                        key: 'number',
                        sort: false,
                        default:true
                    },{
                        title: '姓名',
                        key: 'name',
                        sort: false,
                        default:true
                    }, {
                        title: '出席狀況',
                        key: 'status',
                        sort: false,
                        default:true
                    }, {
                        title: '備註',
                        key: 'ps',
                        sort: false,
                        default:true
                    }, {
                        title: '最後更新',
                        key: 'updated_by',
                        sort: false,
                        default:true

                    }, ],


                filter: [],
                
             
            }
        },
        beforeMount() {
           this.init()
        },
        methods: {
            init() {
                
                if(this.hide_create) this.createText=''
            },
            getStatusLabel(student){
               return LessonParticipant.getStatusLabel(student)
            }
           
            
        },

    }
</script>