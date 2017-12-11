<template>
   <modal  :show="show" effect="fade"  :width="width">
      <div slot="modal-header" class="modal-header">
         <button id="close-button" type="button" class="close" @click="cancel">
               <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
            <h3>
               請選擇課程
            </h3>
         </div>  
      <div slot="modal-body" class="modal-body">
         <combination-select v-show="active"  :search_params="search_params"
            :disable_terms="disable_terms" :disable_centers="disable_centers"  
            @ready="onCombinationReady">                           
         </combination-select>
      </div>
      <div slot="modal-footer" class="modal-footer" >
         <button type="button" class="btn btn-success" @click.prevent="selected">確定</button>
      </div>
   </modal>
</template>

<script>
export default {
   name: 'SignupCourseSelector',
   props: {
      show:{
         type: Boolean,
         default: false
      },
      disable_terms:{
            type: Boolean,
            default: false
      },
      disable_centers:{
            type: Boolean,
            default: false
      },
      
   },
   data(){
      return {
         width:600,
         search_params:{
            term:0,
            center:0,
            reviewed:1
         },
         course_id:0
      }
   },
   computed:{
      active(){
         return Helper.isTrue(this.show)
      }
   },
   methods: {
      onCombinationReady(course_id){
          this.course_id=course_id
      },
      selected(course_id){
         this.$emit('selected' , this.course_id)
      },
      cancel(){
         this.$emit('cancel')
      }
   }
}
</script>

