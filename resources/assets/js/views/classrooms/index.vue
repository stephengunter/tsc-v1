<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-bell-o" aria-hidden="true"></i> 教室管理</h4>
            </span>
            
            <div>
                <select v-model="center"  @change="doSearch" class="form-control" >
                    <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table v-show="hasData" class="table table-striped" style="width: 95%;">
             <thead> 
                <tr> 
                    <th style="width:15%"></th> 
                    <th style="width:20%">名稱</th> 
                    <th style="width:20%">狀態</th> 
                    <th style="width:20%">備註</th>
                    <th style="width:15%">最後更新</th> 
                    <th></th>
                </tr> 
               
            </thead>
            <tbody > 
                
                <edit v-if="creating" :center_id="center"  @saved="onCreated"  @canceled="onCreateCanceled" > </edit>  
            
                <edit  v-for="classroom in classroomList"  :classroom="classroom" 
                    @saved="onUpdated"  @btn-delete-clicked="beginDelete" >
                </edit>

               
                
            </tbody>
            
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeleteCanceled" @confirmed="deleteClassroom">        
    </delete-confirm>

</div>

</template>

<script>
    import Edit from '../../components/classroom/edit.vue'
    export default {
        name: 'Classroom',
        components: {
             Edit,
        },
        beforeMount() {
           let options=this.loadOptions()
           options.then(value => {
                       this.center=value
                       this.init()
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                    })
           
        },
        computed:{
            hasData(){
                if(this.classroomList.length) return true
                return false    
            },
           
        },
        data() {
            return {
                loaded:false,
                creating:false,
                center:0,
                centerOptions:[],
                
                classroomList:[],

                

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

                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }
                this.classroomList=[]


                this.fetchData()
            }, 
            loadOptions(){
                return new Promise((resolve, reject) => {
                    let options=Center.options()
                    options.then(data => {
                        this.centerOptions=data.options
                        resolve(this.centerOptions[0].value);
                    })
                    .catch(error => {
                        reject(error);
                    })
                })   //End Promise
            },
            fetchData() {
                let getData=Classroom.index(this.center)
                    getData.then(data => {
                      
                       this.classroomList=data.classroomList
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
            deleteClassroom(){
                let id = this.deleteConfirm.id 
                let remove= Classroom.delete(id)
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
            
            onCreated(classroom){    
                this.init()
            },
            onCreateCanceled(){
                this.init()
            },
            onUpdated(classroom){ 
                  this.init()
            },
            
           
        },

    }
</script>