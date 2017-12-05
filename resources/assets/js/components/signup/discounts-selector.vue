<template>
   <div>
      <toggle :items="discountOptions"   :default_val="discount_id" @selected="countTuition"></toggle>
   </div>
</template>

<script>
export default {
   name: 'DiscountSelector',
   props: {
		date:{
         type: String,
         default: ''
      },
      discount_id:{
         type: Number,
         default: 0
      },
      course_id:{
         type: Number,
         default: 0
      }
      
   },
   beforeMount() {
      this.init()
   },
   data() {
      return {
         
         discountOptions:[],
        selected:0,
        tuition:0,
      }
	},
	watch: {
		date(){
			this.init()
		},
		course_id(){
			
			this.init()
		}
	},
   methods: {
      init() {
			
         this.fetchData() 
      },
      fetchData(){
         let getData=Discount.options(this.course_id,this.date)
                
         getData.then(data => {
            let options=data.options
            
            let noDiscount={ text:'無' , value:'0' }
            options.splice(0, 0, noDiscount)
            this.discountOptions=options

            this.countTuition(this.discount_id)
        })
        .catch(error=> {
            Helper.BusEmitError(error)                        
        })
      },
      countTuition(val) {
         this.selected=val
        
         let action=Discount.countTuition(this.course_id,val,this.date)
              
         action.then(data => {
				this.tuition=data.tuition
				
				this.$emit('selected',data)
			})
			.catch(error=> {
				  Helper.BusEmitError(error)                        
			})
      },  
   }
}
</script>

