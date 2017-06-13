<template>
    <div v-if="loaded" class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span>    
            <div>
                <button v-show="can_back"  @click="onBtnBackClick" class="btn btn-default btn-sm" >
                   <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                   返回
                </button>
                <button  v-if="volunteer.canEdit" v-show="can_edit" @click="onBtnEditClicked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                </button>
                <button v-if="volunteer.canDelete" v-show="can_edit" @click="onBtnDeleteClicked" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
                </button>
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
      
            <div class="row">
                 
                 <div class="col-sm-3">
                      <label class="label-title">姓名</label>
                      <p v-text="volunteer.user.profile.fullname"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">稱謂</label>
                      <p v-text="volunteer.user.profile.titleText"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">狀態</label>
                      <p v-html="$options.filters.activeLabel(volunteer.active)">                       
                      </p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">加入日期</label>
                      <p v-text="volunteer.join_date"></p>                      
                 </div>
                
            </div>   <!-- End row-->
       
        </div><!-- End panel-body-->
    </div>
  


  


</template>

<script>
   
    export default {
        name: 'ShowVolunteer',
        props: {
            id: {
              type: Number,
              default: 0
            },
            version: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            can_back:{
               type: Boolean,
               default: true
            },
        },
        data() {
            return {
               title:Helper.getIcon('Volunteers')  + '  志工資料',
               loaded:false,
               volunteer:null,
            }
        },
        watch:{
          'version' : 'init'
        },
        beforeMount(){
           this.init()
        },
        methods: {
           init(){
            
              this.loaded=false
              this.volunteer=null
              if(this.id) this.fetchData()
              
           },
           fetchData() {
                let getData = Volunteer.show(this.id)             
             
                getData.then(data => {
                   this.volunteer= data.volunteer
                   this.$emit('dataLoaded',this.volunteer)
                   this.loaded = true                        
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            },
            onBtnEditClicked(){   
              this.$emit('begin-edit') 
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },
            onBtnDeleteClicked(){
                 let values={
                    name: this.volunteer.user.profile.fullname,
                    id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',parseInt(this.volunteer.updated_by))
            },
            
          
        }, 
    }
</script>
