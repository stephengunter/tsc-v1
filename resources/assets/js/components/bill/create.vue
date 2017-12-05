<template>
   <div v-if="loaded" class="panel panel-default">
      <div class="panel-heading">
         <span class="panel-title">
				<h4 v-html="title"></h4>
			</span>  
      </div>
      <div class="panel-body">
         <div class="row">
            <div class="col-sm-3">
               <div class="form-group"> 
                  <h3>學員姓名：何金水</h3>
                       
               </div>  
            </div>
         </div>
         <div class="row">
            <div class="col-sm-12">
               <div class="form-group"> 
                  <h3>報名課程：</h3>
                  
                  <ul style="font-size:1.2em">
                     <li v-for="(signup,index) in signups" :key="index">{{ signup.course.fullname }}
								 &nbsp;&nbsp;{{ signup.course.tuition | formatMoney }}
							</li>
                    
                  </ul> 
               </div>  
            </div>
         </div>
         <div class="row">
            <div class="col-sm-12">
               <div class="form-group"> 
                  <h3>合計：{{ form.bill.total | formatMoney }}
               
                  </h3>
               </div>  
            </div>
         </div>
			<div  class="row">
				<div class="col-sm-12">
					<div class="form-group"> 
						<h3>折扣優惠</h3>
						<discounts-selector :discount_id="this.form.bill.discount_id" :discounts="discounts"
						@selected="onDiscountSelected">
						</discounts-selector>
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
			<div class="row" >
				<div  class="col-sm-6">
					<button type="button"  class="btn btn-success" @click.prevent="onSubmit">確定</button>
					
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button class="btn btn-default" @click.prevent="canceled">取消</button>
				</div>  

			</div>  
      </div>    
   </div>
</template>

<script>
import DiscountSelector from "./discounts-selector.vue"
import TuitionInputs from "../../components/tuition/inputs.vue"
export default {
   name: 'CreateBill',
   components: {
		'discounts-selector': DiscountSelector,
		'tuition-inputs':TuitionInputs
	},
   props: {
		user_id: {
			type: Number,
			default: 0
		},
		
	},
   data() {
		return {

				title: Helper.getIcon('bills') + '  繳費',
				loaded: false,
				signups: [],
				form:{},

				discounts:[],
				payways:[]
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
			let getData=Bill.create(this.user_id)
				
			getData.then(data => {
				this.signups=data.signups
				this.form=new Form({
					bill:data.bill,
					tuition:data.tuition
				})

				this.payways=data.payways
				
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

				this.loaded=true
			})
			.catch(error=> {
				Helper.BusEmitError(error) 
			})
            
		},
		onDiscountSelected(id){
			
			  let discount=this.discounts.find((item)=>{
				  return item.value==id
			  })

			  let points=parseInt(discount.points)
			  

			  if(points){
				  this.form.bill.amount=points * this.form.bill.total /100
			  }else{
				   this.form.bill.amount=this.form.bill.total
			  }

			  this.form.tuition.amount=this.form.bill.amount
		},
		onSubmit(){
			let save=Bill.store(this.form)
                
			save.then(data => {

				Helper.BusEmitOK()
				this.$emit('saved',data)                            
			})
			.catch(error => {
				Helper.BusEmitError(error) 
			})
		},
		canceled(){
			
		}
   }
}
</script>

