<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <h4 v-html="title"></h4>
            </span>
            <div>
                <button v-show="!hide_create" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table  class="table table-striped" style="width: 99%;">
             <thead> 
                <tr> 
                    <th style="width:22%">姓名</th> 
                    <th style="width:10%">假別</th> 
                    <th style="width:35%">時間</th>
                    <th style="width:15%">備註</th>
                    <th style="width:10%">最後更新</th> 
                    <th style="width:8%"></th>
                </tr> 
            </thead>
            <tbody> 
                <edit v-for="leave in leaveList"  :leave="leave" @canceled="onEditCanceled"
                     @saved="onUpdated"  @btn-delete-clicked="onBtnDeleteClicked" >
                </edit>
                <edit v-if="creating" :lesson_id="lesson_id"  @saved="onCreated"  @canceled="onCreateCanceled" > </edit> 
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteLeave">        
    </delete-confirm>       

    

</div>

</template>

<script>
    import Edit from './edit.vue'
    export default {
        name: 'LeaveList',
        components: {
             Edit,
        },
        props: {
            lesson_id: {
              type: Number,
              default: 0
            },
            user_id: {
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
            can_select:{
               type: Boolean,
               default: true
            },
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.leaveList.length) return true
                return false    
            }
        },
        data() {
            return {
                title:Helper.getIcon('leaves') + '  缺席/請假紀錄',
                loaded:false,
                creating:false,
                leaveList:[],
              
                orderOptions:{},

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
              
                this.leaveList=[]
                
                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:'',

                }
                
                this.fetchData()         
            }, 
            fetchData() {
                let lesson=this.lesson_id
                let user=this.user_id
                let index=Leave.index(lesson,user)
                index.then(data => {
                   
                   this.leaveList=data.leaveList
                   this.loaded = true
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            onEditCanceled(){
                this.init()
            },
            beginCreate(){
                this.creating=true
            },
            onCreateCanceled(){
                 this.init()
            },
            onBtnDeleteClicked(values){
                this.deleteConfirm.msg='確定要刪除紀錄： ' + values.name + ' 嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true     
                
            },
            onCreated(){    
                this.init()
                this.$emit('created')
            },
            onUpdated(){
                this.init()
                this.$emit('updated')
            },            
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteLeave(){
                let id = this.deleteConfirm.id 
                let remove= Leave.delete(id)
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
            
           
        },

    }
</script>