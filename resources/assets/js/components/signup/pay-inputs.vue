<template>

<div v-if="form">
	<div  class="row">
		<div class="col-sm-12">
			<div class="form-group"> 
				<label>折扣優惠</label>
				<discounts-selector :course_id="getCourseId()" :discount_id="getDiscountId()"
				:date="form.signup.date" @selected="onDiscountSelected">
				</discounts-selector>
			</div>  
		</div>
			
	</div> 
	<div class="row">
		<div  class="col-sm-3">
			<div class="form-group"> 
				<label>應繳學費</label>
				<input  type="text" name="signup.tuition" class="form-control" v-model="form.signup.tuition" @keydown="clearErrorMsg('signup.tuition')"  >
				<small class="text-danger" v-if="form.errors.has('signup.tuition')" v-text="form.errors.get('signup.tuition')"></small>
			</div>  
		</div>
		<div v-if="false" class="col-sm-3">    
			<div class="form-group"> 
				<label>材料費</label>
				<input disabled type="text" name="signup.cost" class="form-control" v-model="form.signup.cost" @keydown="clearErrorMsg('signup.cost')"  >
				<small class="text-danger" v-if="form.errors.has('signup.cost')" v-text="form.errors.get('signup.cost')"></small>
			</div> 
		</div>
		<div v-if="false"  class="col-sm-3">    
			<div class="form-group"> 
				<label>合計應繳</label>
				<input disabled type="text" :value="total" name="signup.cost" class="form-control"  >
				
			</div> 
		</div>
	</div>   <!--  row   -->
	<tuition-inputs  v-if="pay" :form="form" :payways="payways"
			@clear-error="clearErrorMsg">
	</tuition-inputs>
   
</div>  
</template>


<script>
import DiscountSelector from "./discounts-selector.vue"
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
		return {};
	},
	computed: {
		total() {
			return Number(this.form.signup.tuition) + Number(this.form.signup.cost);
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
		init() {},
		getCourseId(){
			return Helper.tryParseInt(this.form.signup.course_id)
		},
		getDiscountId(){
			return Helper.tryParseInt(this.form.signup.discount_id)
		},
		onDiscountSelected(discount) {
			
			this.$emit("discount-selected", discount);
		},
		clearErrorMsg(name) {
			this.$emit("clear-error", name);
		}
	}
};
</script>