<template>

<div v-if="form">
   <div class="row">
      <div class="col-sm-4">
          <div class="form-group">                           
              <label>名稱</label>
              <input type="text" name="teacher.name" class="form-control" v-model="form.teacher.name"  >
              <small class="text-danger" v-if="form.errors.has('teacher.name')" v-text="form.errors.get('teacher.name')"></small>
       
          </div>
      </div>
      <div class="col-sm-4">
          <div class="form-group">
              <label>狀態</label>
              <div>
                  <toggle :items="activeOptions"   :default_val="form.teacher.active" @selected=onActiveSelected></toggle>
              </div>
          </div>
      </div>
      <div class="col-sm-4">
          <div class="form-group">
              <label>資料審核</label>
              <div>
              <input type="hidden" v-model="form.teacher.reviewed"  >
              <toggle :items="reviewedOptions"   :default_val="form.teacher.reviewed" @selected=onReviewedSelected></toggle>
              </div>
          </div>
      </div>
   </div>   <!--  row   -->
  
  
   <div class="row">
        <div class="col-sm-8">
            <div class="form-group">
                <label>簡介</label>
                <textarea rows="6" cols="50" class="form-control" name="teacher.description"  v-model="form.teacher.description">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></small>
            </div>
        </div>
        <div class="col-sm-4">
           
        </div>

   </div>  <!-- end row-->
   <div class="row">
       <div class="col-sm-4">
           <div class="form-group">                           
               <button type="submit"  class="btn btn-success" :disabled="form.errors.any()">確認送出</button>
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               <button type="button" class="btn btn-default" @click.prevent="onCanceled">取消</button>  
           </div>
        </div>
      
   </div>
</div>  
</template>


<script>
  export default {
      name: 'TeacherGroupInputs',
      props: {
            form: {
               type: Object,
               default: null
            },
      },
      data() {
            return {
               
                activeOptions:Helper.activeOptions(),
                reviewedOptions:Helper.reviewedOptions()
            }
      },
      computed:{
          
        
      },
      beforeMount() {
           this.init()
      },
      methods: {
          init() {
              
          },
          onActiveSelected(val){
              this.$emit('active-selected',val)
          },
          
          onReviewedSelected(val){
              this.$emit('reviewed-selected',val)
          },
          onCanceled(){
             this.$emit('canceled')
          }
      }
  }
</script>