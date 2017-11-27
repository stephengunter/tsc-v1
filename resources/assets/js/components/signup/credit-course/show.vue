<template>
    <div v-if="loaded" class="panel panel-default show-data">
      <div class="panel-heading">
			<span class="panel-title">
				<h4 v-html="title"></h4>
			</span>    
			<div>
				<button v-show="can_back"  @click="onBtnBackClick" class="btn btn-default btn-sm" >
					<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
					返回
				</button>
				<button v-if="signup.payed()" @click="btnPrintClicked" class="btn btn-warning btn-sm" >
					<span class="glyphicon glyphicon-print" aria-hidden="true"></span> 列印收據
				</button>
				<button  v-if="signup.canEdit" v-show="can_edit" @click="btnEditClicked" class="btn btn-primary btn-sm" >
					<span class="glyphicon glyphicon-pencil"></span> 編輯
				</button>
				<button v-if="signup.canDelete" v-show="can_edit" @click="btnDeleteClicked" class="btn btn-danger btn-sm" >
					<span class="glyphicon glyphicon-trash"></span> 刪除
				</button>
			</div>
      </div>  <!-- End panel-heading-->
      <div class="panel-body" v-if="loaded">
			<div class="row">
				<div class="col-sm-3">
					<label class="label-title">報名日期</label>
					<p v-text="signup.date"></p>   	                     
				</div>
				<div class="col-sm-3">
					<label class="label-title">姓名</label>
					<p v-text="signup.user.profile.fullname"></p>                 
				</div>
				<div class="col-sm-3">
					<label class="label-title">課程名稱</label>
					<p v-html="signup.formatedCourseName"></p>  
						
				</div>
				<div class="col-sm-3">
						<label class="label-title">網路報名</label>
						<p>{{ signup.net_signup | boolText }}</p>
				</div>
			</div>   <!-- End row-->
			<div class="row">
				<div class="col-sm-3">
					<label class="label-title">狀態</label>
					<p v-html="signup.statusLabel()"></p>   
				</div>
				<div class="col-sm-3">
					<label class="label-title">課程費用</label>
					<p>
						{{ signup.tuition | formatMoney }}
						&nbsp;&nbsp;
						{{ getDiscountText() }}
					</p>                      
				</div>
				<div class="col-sm-3">
					<label class="label-title">教材費</label>
					<p>
						{{ signup.cost | formatMoney }}
					</p>   
				</div>
				<div class="col-sm-3">
					<label class="label-title">最後更新</label>
					<updated :entity="signup"></updated>
							
				</div>
                  
			</div>   <!-- End row-->
      </div><!-- End panel-body-->
    </div>
  


  


</template>

<script>
export default {
	name: "ShowSignup",
	props: {
		id: {
			type: Number,
			default: 0
		},
		version: {
			type: Number,
			default: 0
		},
		can_edit: {
			type: Boolean,
			default: true
		},
		can_back: {
			type: Boolean,
			default: true
		}
	},
	data() {
		return {
			title: Helper.getIcon("Signups") + "  報名表",
			loaded: false,
			signup: null
		};
	},
	watch: {
		version: "init"
	},
	beforeMount() {
		this.init();
	},
	methods: {
		init() {
			this.loaded = false;
			this.signup = null;
			if (this.id) this.fetchData();
		},
		fetchData() {
			let getData = Signup.show(this.id);

			getData
			.then(data => {
				let signup = data.signup;
				this.signup = new Signup(signup);
				this.$emit("dataLoaded", signup);
				this.loaded = true;
			})
			.catch(error => {
				Helper.BusEmitError(error);
			});
		},
		signupDate(date) {
			return Helper.tpeDate(date);
		},
		getDiscountText(){
			if(!this.signup.discountText) return ''
			return '( ' +  this.signup.discountText + ')'
		},
		btnEditClicked() {
			this.$emit("begin-edit");
		},
		onBtnBackClick() {
			this.$emit("btn-back-clicked");
		},
		btnDeleteClicked() {
			let values = {
			name: this.signup.user.profile.fullname,
			id: this.id
			};
			this.$emit("btn-delete-clicked", values);
		},
		btnPrintClicked() {
			this.$emit("print-invoice", this.id);
		}
	}
}
</script>
