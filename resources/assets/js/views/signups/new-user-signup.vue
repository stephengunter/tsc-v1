<template>
<div v-if="loaded">
    <div class="panel panel-default">
        
        <div class="panel-heading">           
            <span class="panel-title">
                <h4 v-html="title"></h4> 
            </span>           
        </div>
        <div class="panel-body">
            <form  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form">
                <user-inputs ref="userinputs"  :form="form" @checked="onUserChecked" @check-failed="onUserCheckFailed">

                </user-inputs>
               
                <div class="row" v-show="user_checked">
                    <div class="col-sm-6">
                        <button v-if="formSubmitting" class="btn btn-default">
                            <i class="fa fa-spinner fa-spin"></i> 
                            處理中
                        </button> 
                        <button v-else type="submit"  class="btn btn-success">確認送出</button>
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-default" @click.prevent="canceled">取消</button>
                    </div>

                </div> 
            </form>
        </div>
    </div>

    <modal :showbtn="false"  :show="userListModal.show" effect="fade" :width="userListModal.width">
        
          <div slot="modal-header" class="modal-header modal-header-danger">
             <button id="close-button" type="button" class="close" data-dismiss="modal" @click="userListModal.show=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
             </button>
             <h3>
                 <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                 相同資料的使用者已經存在
             </h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <user-selector :users="userListModal.users" @selected="onUserSelected"></user-selector>
        </div>
    </modal>
</div>       
</template>

<script>
	import UserInputs from '../../components/signup/user-inputs.vue'
	import UserSelector from '../../components/user/selector.vue'
	export default {
		name: 'NewUserSignup',
		components: {
			'user-inputs':UserInputs,
			'user-selector':UserSelector,
			
		},
		props: {
			user_valid:{
				type: Boolean,
				default: false
			},
		},
		data() {
			return {
				loaded:false,
				title:Helper.getIcon('Signups')  + '  新學員報名',
				form : new Form({
					user:{
						email:'',
						phone:'',
						profile:{
							fullname:'',
						}
					},
					
				}),
				formSubmitting:false,
				user_checked: false,
				

				userListModal:{
					show:false,
					width:1200,
					
					users:[]
				},

			}
		},
		computed: {
			
			
		},
		watch:{
			
			
		},
		beforeMount() {
			this.init()
		},
		methods: {
			
			init() {
				this.loaded=false
				this.formSubmitting=false
				this.user_checked= false
				
			
				this.fetchData()
					
			},
			fetchData(){
				let role=Signup.roleName()
				let create=User.create(role)
				create.then(data=>{
					
					let user=data.user

					this.form=new Form({
						user:data.user
						
					})

					

					this.loaded=true
					
				}).catch(error =>{
					Helper.BusEmitError(error)
				})
			},
			canceled(){
				this.init()
			},
			onUserChecked(user){
				this.user_checked=true

				this.form.user.phone=user.phone
				this.form.user.email=user.email
				this.form.user.profile.fullname=user.profile.fullname

				if(this.formSubmitting) {
					this.submitForm()
				}

			},
			onUserCheckFailed(users){
				this.formSubmitting=false
				this.userListModal.users= users
				if(users.length){
					this.userListModal.show=true
				}
			},           
			onUserSelected(selected){
				let id=selected[0]
				
				this.redirectToUser(id)
			},
			clearErrorMsg(name) {
				this.form.errors.clear(name)
			},
			onSubmit() {
				this.formSubmitting=true
				this.$refs.userinputs.checkUser()
			},
			submitForm() {

				this.form.user.name=this.form.user.profile.fullname

				let store=User.store(this.form)
					.then(user => {
						Helper.BusEmitOK()
						this.formSubmitting=false

						this.redirectToUser(user.id)
													
					})
					.catch(error => {
						this.formSubmitting=false
						Helper.BusEmitError(error,'存檔失敗') 
						
						
					})
			},
			redirectToUser(id){
				Helper.redirect('/users/' + id)
			}
			
			
		




		},

    }
</script>