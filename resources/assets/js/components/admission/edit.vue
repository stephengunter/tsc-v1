<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">           
            <div class="panel-title">
                 <h4 v-html="title"></h4>                  
            </div> 
            <div class="center-block">
              以下是有效的報名紀錄&nbsp;&nbsp;&nbsp;  順序：1.已繳費  2.報名日期
            </div>  
            <div>
                已選擇：33
            </div>      
        </div>
        <div v-if="course" class="panel-body">
             <table class="table table-striped" style="width: 99%;">
                <thead> 
                    <tr> 
                        <th v-for="item in thead">{{ item.title }}</th> 
                    </tr> 
                </thead>
                <tbody> 
                    <row v-for="item in admitList" :admit="item"
                      :can_select="rowSettings.can_select" 
                      :show_updated="rowSettings.show_updated"
                      :can_edit="rowSettings.can_edit">
                         
                    </row>
                </tbody>
            
            </table>
        </div>
    </div>
</div>
  


  


</template>

<script>
    import Row from './row.vue'
    export default {
        name: 'EditAdmission',
        components: {
            Row,
        },
        props: {
            course_id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                title:Helper.getIcon(Admission.title()) + '  建立錄取名單',
                rowSettings:{
                    can_select:true,
                    show_updated:false,
                    can_edit:false
                },
                
                form:{},
                course:null,
                admitList:[],

                thead:Admission.getThead(true),
               
            }
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init(){
                this.fetchData()

            },
            fetchData(){
                let getData=Admission.create(this.course_id)
                getData.then(data => {
                    this.admitList=data.admitList
                    this.course=data.course
                    this.form = new Form({
                            admission: {
                                course_id:this.course_id
                            }
                    })
                    // let item = this.thead.findIndex()( item=>{
                    //    return item.key == 'updated_by'
                    // })
                    let index=this.thead.findIndex(item=>{
                        return item.key == 'updated_by'
                    })
                    
                    this.thead.splice(7,1)
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
          
        }, 
       
       
    }
</script>
