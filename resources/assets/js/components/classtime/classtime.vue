<template>
<div>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-clock-o" aria-hidden="true"></i> 課程時間</h4>
            </span>
            
            <div>
                <button v-if="can_edit" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table  v-show="hasData || creating"  class="table table-striped" style="width: 95%;">
                <thead> 
                    
                    <tr> 
                        <th style="width:35%">上課時間</th> 
                        <th style="width:15%"></th> 
                        <th style="width:15%"></th> 
                        <th style="width:25%">最後更新</th>
                        <th style="width:10%"></th>
                    </tr> 
                   
                </thead>
                <tbody> 
                
                    <edit v-if="creating" :course_id="course_id"  @saved="onCreated" 
                       @canceled="onCreateCanceled" > 
                    </edit>  

                    <edit  v-for="classtime in classtimeList"  :classtime="classtime" 
                        :can_edit="can_edit"
                        @editting="onEditting" @canceled="onEditCanceled"
                        @saved="onUpdated"  @btn-delete-clicked="beginDelete" >
                    </edit>

               
                
                </tbody>
            
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
           

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="onDeleteCanceled" @confirmed="deleteClasstime">        
    </delete-confirm>

</div>

</template>

<script>
    import Edit from '../../components/classtime/edit.vue'
    export default {
        name: 'Classtime',
        components: {
             Edit,
        },
        props: {
            course_id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.classtimeList.length) return true
                return false    
            },
            indexMode(){
                if(this.creating) return false
                if(this.seleted) return false
                    return true
            }
        },
        data() {
            return {
                loaded:false,
                creating:false,
                seleted:0,
                classtimeList:[],

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
                this.seleted=0
                
                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }

                this.classtimeList=[]
                this.fetchData()
            },
            fetchData() {
                let getData=Classtime.index(this.course_id)
                    getData.then(data => {
                      
                       this.classtimeList=data.classtimeList
                       this.loaded = true
                        
                    })
                    .catch(error => {
                        Helper.BusEmitError(error)
                    })
            },
            
            beginCreate(){
                this.creating=true
            },
            endCreate(){
                 this.creating=false
            },
            onEditting(id){
                this.seleted=id
            },
            OnEditCanceled(){
                this.seleted=0
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
            deleteClasstime(){
                 let id = this.deleteConfirm.id 
                let remove= Classtime.delete(id)
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
            onCreated(classtime){    
                this.init()
                this.$emit('created',classtime)
            },
            onCreateCanceled(){
                this.init()
            },
            onEditCanceled(){
                this.init()
            },
            onUpdated(classtime){ 
                 this.init()
                 this.$emit('updated',classtime)
            },
            
           
        },

    }
</script>