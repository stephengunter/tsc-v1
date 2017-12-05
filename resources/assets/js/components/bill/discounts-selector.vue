<template>
   <div>
      <toggle :items="discounts"   :default_val="discount_id" @selected="onSelected"></toggle>
   </div>
</template>

<script>
export default {
   name: 'DiscountSelector',
   props: {
      discount_id:{
         type: Number,
         default: 0
      },
      discounts:{
         type: Array,
         default: null
      }
      
   },
   beforeMount() {
		
	  	this.countTuition(this.discount_id)
   },
   data() {
      return {
         
			selected:0,
			
      }
	},
   methods: {
      countTuition(val) {
			this.selected=val
			

			this.$emit('selected', this.selected)
         
      },  
   }
}
</script>

