<template>
<div class="panel panel-default show-data">
    <div class="panel-heading">
        <span class="panel-title">
           <h4><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> 課程分類</h4>
        </span>
              
        <div>
             <button v-if="category.canEdit"  @click="btnEditClicked" class="btn btn-primary btn-sm" >
                <span class="glyphicon glyphicon-pencil"></span> 編輯
             </button>
              <button v-if="category.canDelete"  @click="beginDelete" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
               </button>
        </div>
    </div>  <!-- End panel-heading-->
    <div class="panel-body">
       
            <div class="row">
                 <div class="col-sm-3">
                      <label class="label-title">分類名稱</label>
                      <p v-text="category.name"></p>                      
                 </div>
                 <div class="col-sm-3">
                      <label class="label-title">小圖</label>
                     
                      <p v-html="$options.filters.showIcon(category.icon)">                       
                      </p>                     
                 </div>
                  <div class="col-sm-3">
                      <label class="label-title">狀態</label>
                      <p v-html="$options.filters.activeLabel(category.active)">                       
                      </p>
                  </div>
                  <div class="col-sm-3">
                      <label class="label-title">最後更新</label>
                      <p v-if="!category.updated_by"> {{   category.updated_at|tpeTime  }}</p>
                      <p v-else>
                        <a  href="#" @click.prevent="showUpdatedBy" >
                            {{   category.updated_at|tpeTime  }}
                        </a>
                        
                      </p>
                  </div>
            </div>   <!-- End row-->
            
           
       
   </div><!-- End panel-body-->
</div>

</template>

<script>
    export default {
        name: 'ShowCategory',
        props: ['category',  'canEdit'],
        methods: {
            
            btnEditClicked(){
                this.$emit('beginEditCategory')
            },
           
            
            beginDelete(){
                this.$emit('beginDelete')
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.category.updated_by)
            },
        }, 

    }
</script>