<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-calendar-o" aria-hidden="true"></i> 假日設定</h4>
            </span>
            <div>
                <select v-model="year"  @change="doSearch" class="form-control" >
                    <option v-for="item in yearOptions" :value="item.value" v-text="item.text"></option>
                </select>
            </div>
            <div>
                <button :disabled="creating" v-if="can_edit" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 90%;">
             <thead> 
                <tr v-if="creating"> 
                   
                    <th style="width:30%">名稱</th> 
                    <th style="width:25%">起始日期</th> 
                    <th style="width:25%">終止日期</th> 
                    <th style="width:20%"></th>
                </tr> 
                <tr v-else> 
                    <th style="width:30%">名稱</th> 
                    <th style="width:25%">日期</th>
                    <th style="width:25%">最後更新</th> 
                    <th style="width:20%"></th>
                </tr> 
            </thead>
            <tbody > 
                
                <edit v-if="creating"  @saved="onCreated"  @canceled="onCreateCanceled" > </edit>  
            
                <edit  v-for="holiday in holidayList"  :holiday="holiday" 
                     @saved="holidayUpdated"  @btn-delete-clicked="beginDelete" >
                </edit>

               
                
            </tbody>
            
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeleteCanceled" @confirmed="deleteHoliday">        
    </delete-confirm>

</div>

</template>

<script>
    import Edit from '../../components/holiday/edit.vue'
    export default {
        name: 'Holiday',
        components: {
             Edit,
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.holidayList.length) return true
                return false    
            }
        },
        data() {
            return {
                loaded:false,
                creating:false,

                can_edit:false,
                year:0,
                yearOptions:[],
                
                holidayList:[],

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
             
            }
        },
        methods: {
            init() {
                this.loaded=false
                this.creating=false
                this.can_edit=false

                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
               }

                this.year=Moment().year()
                this.yearOptions=Helper.yearOptions()

                this.holidayList=[]
                
               
                this.fetchData()
            }, 
            
            fetchData() {
                let getData=Holiday.index(this.year)
                    getData.then(data => {
                      this.can_edit=data.canEdit
                       this.holidayList=data.holidayList
                       this.loaded = true
                        
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                    })
            },
            doSearch(){
               this.fetchData()
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            beginCreate(){
                this.creating=true
            },
            endCreate(){
                this.creating=false
            },
            cancelCreate(){
                this.creating=false
            },
            beginDelete(values){
                this.deleteConfirm.msg='確定要刪除 ' + values.name + ' 嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                
            },
            
            onDeleteCanceled(){
                this.deleteConfirm.show=false
            },
            deleteHoliday(){
                let id = this.deleteConfirm.id 
                let remove= Holiday.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.init()
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },
            
            onCreated(holiday){    
                this.init()
            },
            onCreateCanceled(){
                this.init()
            },
            holidayUpdated(holiday){ 
                  this.init()
            },
            
           
        },

    }
</script>