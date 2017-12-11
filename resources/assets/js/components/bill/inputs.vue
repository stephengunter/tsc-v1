<template>
   <div v-if="form">
      <div class="row">
         <div class="col-sm-12">
            <div class="form-group"> 
               <h3>折扣優惠</h3>
               <div>
						<toggle :items="discount_options"   :default_val="discount_index" @selected="onDiscountSelected"></toggle>
					</div>
            </div>  
         </div>
            
      </div> 
		<div v-if="can_edit"  class="row">
			<div  class="col-sm-3">
				<label>應繳金額</label>
				<input type="text" name="bill.amount" class="form-control" v-model="form.bill.amount">
            <small class="text-danger" v-if="form.errors.has('bill.amount')" v-text="form.errors.get('bill.amount')"></small>
              
			</div>
			<div  class="col-sm-9">
				<div class="form-group">
                <label>備註</label>
                <input type="text" name="tuition.ps" class="form-control" v-model="form.signup.ps"   >
            
            </div>
			</div>
		</div>
      <div v-else class="row">
			
         <div class="col-sm-12">
            
				<div class="form-group">
					 <h3>應繳金額： <span style="color:red"> {{ form.bill.amount | formatMoney }}  </span> 
            
               </h3>
				</div> 
         </div>
			
      </div>
      <tuition-inputs :form="form" :payways="payways" :edit_date="can_edit"></tuition-inputs>
   </div>
</template>

<script>
import TuitionInputs from "../../components/tuition/inputs.vue"
export default {
   name: 'BillInputs',
   components: {
		
		'tuition-inputs':TuitionInputs
	},
   props: {
		form: {
			type: Object,
			default: null
		},
		
      date: {
			type: String,
			default: ''
      },
      payways:{
         type: Array,
			default: null
		},
		can_edit: {
			type: Boolean,
			default: false
      },
		
		
	},
   data() {
		return {
			discounts:[],
			discount_options:[],
			discount_index:0,
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
		},
		date(){
			this.init()
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
			
			let course_id=this.form.signups[0].course_id
			let course_count=this.form.signups.length
         let discounts=Bill.discountOptions(course_id,course_count,this.date)
        
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

				this.populateOptions()
         })
         .catch(error=> {
             Helper.BusEmitError(error) 
         })
		},
		populateOptions(){
			this.discount_options=[]
			for(let i=0; i<this.discounts.length;i++){
				let discount= this.discounts[i]
				this.discount_options.push({
					text:discount.text,
					value:i
				})
			}
		},
		onDiscountSelected(index){
			this.discount_index=index

			let discount=this.discounts[index]
			
			
			this.form.bill.discount_id=discount.value
			
			if(discount.identity_id){
				this.form.bill.identity_id=discount.identity_id
			}else{
				this.form.bill.identity_id=0
			}
			
			this.countAmount()
			  
		},
		countAmount(){
			
			let discount=this.discounts.find((item)=>{
				return item.value==this.discount_id
			})
			

			let points=parseInt(discount.points)
			this.form.bill.points=points
			
			let amount=0
			if(points){
				  amount=points * this.form.bill.total /100
			}else{
				 amount=this.form.bill.total
			}
			this.form.bill.amount=Helper.formatMoney(amount)
			this.form.tuition.amount=Helper.formatMoney(amount)
		}
		
   }
}
</script>