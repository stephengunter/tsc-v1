<template>
<div>
    <div  v-if="course"  class="panel panel-default">
        <div class="panel-heading">           
            <div class="panel-title">
                 <h4 v-html="title"></h4>                  
            </div> 
            <div class="center-block">
              以下是有效的報名紀錄&nbsp;&nbsp;&nbsp;  順序：1.已繳費&nbsp;&nbsp;2.報名日期
            </div>  
            <div>
              人數上限：{{ course.limit }}&nbsp;&nbsp;
              最低：{{ course.min }}&nbsp;&nbsp;
              已選擇：<strong class="text-primary" v-text="selectedSignups.length"></strong>    &nbsp;&nbsp;
                
            </div>
            <div>
                <form @submit.prevent="onSubmit" >
                    <button type="submit" class="btn btn-success btn-sm" 
                    :disabled="!canSubmit">確認送出
                    </button>
                </form>
            </div>      
        </div>
        <div class="panel-body">
             <table class="table table-striped" style="width: 99%;">
                <thead> 
                    <tr> 
                        <th v-for="item in thead">{{ item.title }}</th> 
                    </tr> 
                </thead>
                <tbody> 
                    <row v-for="(item, index) in admitList" :admit="item"
                          :index="index+1"
                          :selected="beenSelected(item.signup_id)"
                          :can_select="rowSettings.can_select" 
                          :show_updated="rowSettings.show_updated"
                          :can_edit="rowSettings.can_edit"
                          @selected="onSelected"
                          @unselected="onUnselected"   >
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
        name: 'CreateAdmission',
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
                
               
                course:null,
                admitList:[],

                thead:[],

                form:{},
                submitting:false,
                selectedSignups:[],
               
            }
        },
        computed: {
            canSubmit() {
               if(this.selectedSignups.length < 1) return false
               return !this.submitting 
            }
        },  
        beforeMount() {
            this.init()
        },
        methods: {
            init(){
                this.fetchData()
                this.thead=Admission.getThead(this.rowSettings.show_updated)

                let thSelect={
                    title: '',
                    key: 'select',
                    sort: false,
                    default:true
                 }
                this.thead.splice(0, 0, thSelect)
                let thOrder={
                    title: '',
                    key: 'order',
                    sort: false,
                    default:true
                 }
                this.thead.splice(0, 0, thOrder)

            },
            fetchData(){
                let getData=Admission.create(this.course_id)
                getData.then(data => {
                    this.admitList=data.admitList
                    this.course=data.course
                    this.selectedSignups=data.selected
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            beenSelected(signup_id){
               return this.selectedSignups.indexOf(signup_id) >= 0
            },
            onSelected(signup_id){
               if ( ! this.beenSelected(signup_id) ){
                    this.selectedSignups.push(signup_id)
               }
            },
            onUnselected(signup_id){
               let index= this.selectedSignups.indexOf(signup_id)
               if(index >= 0)  this.selectedSignups.splice(index, 1);
               
            },
            onSubmit(){
                if(this.selectedSignups.length < 1) return false
                this.submitting=true   
                
                this.form=new Form({
                    course_id:this.course_id,
                    selected:this.selectedSignups
                })

                let store = Admission.store(this.form)
                store.then(data => {
                    Helper.BusEmitOK()
                    this.$emit('saved',data)
                })
                .catch(error => {
                  
                    Helper.BusEmitError(error)                        
                })
            }
          
        }, 
       
       
    }
</script>
