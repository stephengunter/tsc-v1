<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-calendar" aria-hidden="true"></i> 學期管理</h4>
            </span>
            <div>
                <button v-if="can_edit" :disabled="creating" class="btn btn-primary btn-sm" @click.prevent="beginCreate">
                   <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 新增
                </button>
            </div>
            
        </div>  <!-- End panel-heading-->
        <div v-if="loaded" class="panel-body">
            <table class="table table-striped" style="width: 95%;">
             <thead> 
                <tr> 
                    <th style="width:10%">年度</th> 
                    <th style="width:7%">順序</th> 
                    <th style="width:12%">名稱</th> 
                    <th >報名期間</th>
                    <th style="width:10%">狀態</th>
                    <th style="width:10%">最後更新</th> 
                    <th style="width:7%"></th>
                </tr> 
            </thead>
            <tbody> 
                <edit v-if="creating"  @saved="onCreated"  @canceled="onCreateCanceled" > </edit>
                <edit v-for="(term,index) in termList" :key="index"  :term="term" @canceled="onEditCanceled"
                     @saved="onUpdated"  @btn-delete-clicked="onBtnDeleteClicked" >
                </edit>
                 
            </tbody> 
            </table>


           

        </div><!-- End panel-body-->

    </div>   
                   
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteTerm">        
    </delete-confirm>       

    

</div>

</template>

<script>
    import Edit from '../../components/term/edit.vue'
    export default {
        name: 'TermIndex',
        components: {
             Edit,
        },
        beforeMount() {
           this.init()
        },
        computed:{
            hasData(){
                if(this.termList.length) return true
                return false    
            }
        },
        data() {
            return {
                loaded:false,
                can_edit:false,
                creating:false,
                termList:[],
              
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
              
                this.termList=[]
                
                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:'',

                }
                
                this.fetchData()         
            }, 
            fetchData() {
                let index=Term.index()
                index.then(data => {
                   this.can_edit=data.can_edit
                   this.termList=data.termList
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
                this.deleteConfirm.msg='確定要刪除 ' + values.name + ' 嗎？'
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
            deleteTerm(){
                let id = this.deleteConfirm.id 
                let remove= Term.delete(id)
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