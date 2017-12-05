<template>

<div v-if="form">
	<div  class="row">
		<div class="col-sm-12">
			<div class="form-group"> 
				<label>折扣優惠</label>
				<discounts-selector :discount_id="getDiscountId()" :discounts="discounts"
						@selected="onDiscountSelected">
				</discounts-selector>
			</div>  
		</div>
			
	</div> 
	<div class="row">
		<div  class="col-sm-3">
			<div class="form-group"> 
				<label>應繳學費</label>
				<input  type="text" name="bill.tuition" class="form-control" v-model="form.bill.tuition" @keydown="clearErrorMsg('bill.tuition')"  >
				<small class="text-danger" v-if="form.errors.has('bill.tuition')" v-text="form.errors.get('bill.tuition')"></small>
			</div>  
		</div>
		
	</div>   <!--  row   -->
	<tuition-inputs  v-if="pay" :form="form" :payways="payways"
			@clear-error="clearErrorMsg">
	</tuition-inputs>
   
</div>  
</template>


<script>
import DiscountSelector from '../../components/bill/discounts-selector.vue'
import TuitionInput from "../tuition/inputs.vue"

export default {
	name: "SignupPayInputs",
	components: {
		'discounts-selector': DiscountSelector,
		'tuition-inputs': TuitionInput
	},
	props: {
		form: {
			type: Object,
			default: null
		},
		payways: {
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
		loaded(){
			if(!this.form) return false
			if(this.discounts.length<1) return false 
			return true
		},
		pay(){
			if(!this.payways)  return false
			return this.payways.length > 0
		}
	},
	beforeMount() {
		this.init();
	},
	methods: {
		init() {
			this.fetchData()
		},
		getDiscountId(){
			return parseInt(this.form.bill.discount_id)
		},
		getCourseId(){
			return this.form.signup.course_id
		},
		getDate(){
			return this.form.signup.date
		},
		fetchData(){
			let getData=Discount.options(this.getCourseId(),this.getDate())
                
         getData.then(data => {
            let discounts=data.discounts
				
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
        })
        .catch(error=> {
            Helper.BusEmitError(error)                        
        })
		},
		onDiscountSelected(id){
			
			  let discount=this.discounts.find((item)=>{
				  return item.value==id
			  })

			  this.$emit('discount-selected',discount)

			//   let points=parseInt(discount.points)
			  

			//   if(points){
			// 	  this.form.bill.amount=points * this.form.bill.total /100
			//   }else{
			// 	   this.form.bill.amount=this.form.bill.total
			//   }

			//   this.form.tuition.amount=this.form.bill.amount
		},
		
		clearErrorMsg(name) {
			this.$emit("clear-error", name);
		}
	}
};
</script>