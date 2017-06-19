<template>
<div v-if="categoryLoaded">

    <show-category v-if="isReadOnly" :category="category" @beginDelete="beginDelete"
    @beginEditCategory="beginEdit" :canEdit="true">        
    </show-category>
    
    <edit-category v-if="!isReadOnly" :id="id"  @saved="onCategorySaved" 
     @endEditcategory="endEdit"   :canEdit="true"></edit-category>
    
    <modal :showbtn="true"  :show.sync="showConfirm" @ok="deleteCategory"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
      <div slot="modal-header" class="modal-header modal-header-danger">
         
          <button id="close-button" type="button" class="close" data-dismiss="modal" @click="closeConfirm">
              <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
          </button>
           <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
      </div>
      <div slot="modal-body" class="modal-body">
          <h3 v-text="confirmMsg"> </h3>
      </div>
   </modal>
</div>
</template>
<script>
    import ShowCategory from '../../components/category/show-category.vue'
    import EditCategory from '../../components/category/edit-category.vue'
    

    export default {
        props:['id','version'],
        components: {
            ShowCategory,
            EditCategory,
            Modal
        },
       
        name: 'Category',
        data() {
            return {
                category:{
                    id:0
                },
                isReadOnly:true,
                categoryLoaded:false,

                showConfirm:false,
                confirmMsg:''
            }
        },
        beforeMount(){
            this.init()
        },
        watch: {
            'id': 'init',
            'version': 'init'
        },
        methods: {
            init() {
               this.category={}
               this.isReadOnly=true
               this.categoryLoaded=false

               this.showConfirm=false
               this.confirmMsg=''

               this.fetchData()
            },
            fetchData() {
                let id=this.id
                let url = '/api/categories/' + id;
                
                axios.get(url)
                    .then(response => {
                        this.category = response.data.category
                        
                        this.categoryLoaded=true
                        this.$emit('loaded',this.category)
                    })
                    .catch(function(error) {
                        console.log(error)
                        this.ready=true
                    })
            },       
            beginEdit() {
                 this.isReadOnly=false
            },
            endEdit(){
                 this.isReadOnly=true
            },
            onCategorySaved(category){
                if(this.id>0){
                   this.init()
                }
                
            },
            beginDelete(){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要刪除此分類嗎？'
                    this.showConfirm=true
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })

            },

            closeConfirm(){
                  this.showConfirm=false
            },
            deleteCategory(){
                let url = '/api/categories/' + this.id 
                let form=new Form()
                form.delete(url)
                .then(result => {
                    Helper.BusEmitOK('刪除成功')
                   
                    this.$emit('deleted')
                    this.closeConfirm();
                })
                .catch(error => {
                                           
                    Helper.BusEmitError('刪除失敗')
                    

                    this.closeConfirm();
                       
                })
            },
        }
    }
</script>
