<template>
<div v-if="loaded" class="panel panel-default show-data">
    <div v-if="admission" class="panel-heading">
        <span class="panel-title">
            報名數：{{ summary.total }} 筆 &nbsp; 
            正取：{{ summary.in }} 筆 &nbsp; 
            備取：{{ summary.out }} 筆 &nbsp; 
          
        </span>    
        <div>
            <button v-if="admission.canDelete" v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                <span class="glyphicon glyphicon-trash"></span> 刪除
            </button>
        </div>
    </div>  <!-- End panel-heading--> 
    <div v-else class="panel-heading">
         
        <div>
            <button  v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
                <span class="glyphicon glyphicon-trash"></span> 刪除
            </button>
        </div>
    </div>  <!-- End panel-heading-->   


</div>  
  


  


</template>

<script>
   
    export default {
        name: 'ShowAdmission',
        props: {
            course_id: {
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
               title:Helper.getIcon(Category.title())  + '  課程分類',
               loaded:false,
               admission:null,
               summary:{
                  total:31,
                  in:20,
                  out:11
               }
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
              this.admission=null
              if(this.course_id) this.fetchData()
              
           },
           fetchData() {
                this.admission={
                    canDelete:true
                }
                this.loaded=true
                // let getData = Category.show(this.id)             
             
                // getData.then(data => {
                //    let category= data.category
                //    this.category=new Category(category)
                //    this.$emit('loaded',category)
                //    this.loaded = true                        
                // })
                // .catch(error=> {
                //     Helper.BusEmitError(error)
                // })
            },
            btnEditClicked(){   
              this.$emit('begin-edit') 
            },
            onBtnBackClick(){
                this.$emit('btn-back-clicked')
            },
            btnDeleteClicked(){
                 let values={
                    name: this.category.name,
                    id:this.id
                }
               this.$emit('btn-delete-clicked',values)
            
            },
          
        }, 
    }
</script>
