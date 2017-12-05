<template>
   <div v-if="form">
      <div class="row">
         <div class="col-sm-12">
            <div class="form-group"> 
               <h3>折扣優惠</h3>
               <div>
						<toggle :items="discounts"   :default_val="discount_id" @selected="onDiscountSelected"></toggle>
					</div>
            </div>  
         </div>
            
      </div> 
      <div class="row">
         <div class="col-sm-12">
            <div class="form-group"> 
               <h3>應繳金額： <span style="color:red"> {{ form.bill.amount | formatMoney }}  </span> 
            
               </h3>
            </div>  
         </div>
      </div>
      <tuition-inputs :form="form" :payways="payways"></tuition-inputs>
   </div>
</template>

<script>
import DiscountSelector from "./discounts-selector.vue"
import TuitionInputs from "../../components/tuition/inputs.vue"
export default {
   name: 'BillInputs',
   components: {
		'discounts-selector': DiscountSelector,
		'tuition-inputs':TuitionInputs
	},
   props: {
		form: {
			type: Object,
			default: null
      },
      center_id: {
			type: Number,
			default: 0
      },
      payways:{
         type: Array,
			default: null
      }
		
	},
   data() {
		return {
			discounts:[]
		}
	},
	computed: {
		discount_id(){
			if(!this.form.bill) return 0
			return Helper.tryParseInt(this.form.bill.discount_id)
			
		},
		total(){
			return this.form.bill.total
		}
	},
	watch:{
		
		total(){
			 this.countAmount()
		}
            
   },
   beforeMount() {
      this.init()
   },
   methods: {
      init(){
         this.fetchData()
      },
      fetchData(){
         let discounts=Bill.discountOptions(this.center_id)
        
         discounts.then(data => {
				let discounts= data.discounts
				
				let default_discount=discounts.find((item)=>{
					 return item.bird
				})

				if(default_discount) {
					this.form.bill.discount_id=default_discount.value
				}
				else{
					let noDiscount={ text:'無' , value:0 , points: 0 }
					discounts.splice(0, 0, noDiscount)
					this.form.bill.discount_id=0
					
				}
				this.discounts=discounts
				
				this.countAmount()
         })
         .catch(error=> {
             Helper.BusEmitError(error) 
         })
      },
		onDiscountSelected(id){
			this.form.bill.discount_id=id
			this.countAmount()
			  
		},
		countAmount(){
			
			let discount=this.discounts.find((item)=>{
				return item.value==this.discount_id
			})

			if(this.discount_id){
				this.form.bill.discount=discount.text
			}else{
				this.form.bill.discount=''
			}

			

			let points=parseInt(discount.points)
			this.form.bill.points=points
			

			if(points){
				  this.form.bill.amount=points * this.form.bill.total /100
			}else{
				this.form.bill.amount=this.form.bill.total
			}

			this.form.tuition.amount=this.form.bill.amount
		}
		
   }
}
</script>