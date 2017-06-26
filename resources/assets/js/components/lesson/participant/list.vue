<template>

<data-viewer :source="source" :thead="thead" :filter="filter"  :title="title" 
    :search_params="searchParams"
    :default_order="defaultOrder" :default_direction="default_direction"
    :default_search="defaultSearch" :version="current_version"
     :create_text="createText"    >
    
       
        <template scope="props">
            <tr>    
               <td>{{ props.item.number }}</td>
               <td>{{ props.item.user.profile.fullname }}</td>
               <td v-html="getStatusLabel(props.item)"></td>
              
               <td v-if="editting(props.item.id)">
                     <textarea rows="3" cols="30" class="form-control" v-model="props.item.ps"> </textarea>  
               </td> 
               <td v-else v-text="props.item.ps"></td>
               
               <td>
                   <updated :entity="props.item"></updated>
               </td> 
               <td v-if="editting(props.item.id)">
                     <button @click.prevent="onSubmit(props.item.id, props.item.ps)"  class="btn btn-success btn-xs">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    </button>  
                     <button  class="btn btn-default btn-xs" @click.prevent="cancelEdit(props.item)">
                         <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                    </button>
               </td> 
               <td v-else>
                   <button  class="btn btn-primary btn-xs" 
                        @click.prevent="beginEdit(props.item.id, props.item.ps)">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </button> 
               </td>
            </tr>
        </template>

</data-viewer>

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
                searchParams:{
                    lesson:0,
                    role:Student.roleName(),
                },
                defaultOrder:'number',
                default_direction:'asc',
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

                    }, 
                    {
                        title: '',
                        key: '',
                        sort: false,
                        default:true

                    }, ],


                filter: [],

                selected:0,
                selectedPS:''
                
             
            }
        },
        beforeMount() {
           this.init()
        },
        methods: {
            init() {
                this.searchParams.lesson=this.lesson_id
                if(this.hide_create) this.createText=''
            },
            getStatusLabel(student){
               return LessonParticipant.getStatusLabel(student)
            },
            beginEdit(id, ps){
                this.selected=id
                this.selectedPS=ps
            },
            editting(id){
                return id == this.selected
            },
            cancelEdit(item){
                item.ps=this.selectedPS
                this.selected=0
            },
            onSubmit(id,ps){
                let form=new Form({
                    ps:ps
                })
                let update=LessonParticipant.update(form,id)
                update.then(data => {
                    Helper.BusEmitOK()
                    
                    this.selected=0
                    this.selectedPS=''
                    this.current_version += 1
                })
                .catch(error => {
                    Helper.BusEmitError(error)                        
                })
            }
           
            
        },

    }
</script>