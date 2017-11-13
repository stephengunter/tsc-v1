<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-bell-o" aria-hidden="true"></i> 教室管理</h4>
            </span>
            
            <div>
                <select v-model="center"  @change="doSearch" class="form-control" >
                    <option v-for="(item,index) in centerOptions" :key="index" :value="item.value" v-text="item.text"></option>
                </select>
            </div>
            <div>
                <button :disabled="creating" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table  class="table table-striped" style="width: 95%;">
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
            
                <edit  v-for="(classroom,index) in classroomList" :key="index"  :classroom="classroom" 
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
            this.init()
           
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

                this.center=0

                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }
                this.centerOptions=[]
                this.classroomList=[]


                this.fetchData()
            }, 
            fetchData() {
                let getData=Classroom.index(this.center)
                    getData.then(data => {
                       
                       if(data.centerOptions){
                           this.centerOptions=data.centerOptions
                           this.center=this.centerOptions[0].value
                       }
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