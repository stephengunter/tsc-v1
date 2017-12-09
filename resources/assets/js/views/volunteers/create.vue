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
                <user-inputs v-if="isNewUser" ref="userinputs"  :form="form" 
					@checked="onUserChecked" @check-failed="onUserCheckFailed">
                </user-inputs>
				<user-inputs v-else ref="userinputs" :checker="false" :form="form"  >
                </user-inputs>
                <div class="row" v-show="user_checked">
                     <div class="col-sm-3">
						<div class="form-group"> 
							<label>稱謂</label>
							<input type="text" name="user.profile.title" class="form-control" v-model="form.user.profile.title" >
							
						</div> 
					</div>

                </div> 
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
	import UserInputs from '../../components/user/inputs.vue'
	import UserSelector from '../../components/user/selector.vue'
	export default {
		name: 'VolunteerCreate',
		components: {
			'user-inputs':UserInputs,
			'user-selector':UserSelector,
			
		},
		props: {
			user_id:{
				type: Number,
				default: 0
			},
		},
		data() {
			return {
				loaded:false,
				title:Helper.getIcon('volunteers')  + '  新增志工',
				form : {},
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
			isNewUser(){
				return this.getUserId() < 1
			},
			isNewVolunteer(){
				return this.getVolunteerId() < 1
			}
			
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
			getUserId(){
				if(!this.form.user.id) return 0
				 return	parseInt(this.form.user.id) 
			},
			getVolunteerId(){
				if(!this.form.volunteer.user_id) return 0
				 return	parseInt(this.form.volunteer.user_id) 
			},
			fetchData(){
				
				let create=Volunteer.create(this.user_id)
				create.then(data=>{
					
					let user=data.user

					this.form=new Form({
						user:data.user,
						volunteer:data.volunteer
						
					})

					if(!this.isNewUser) this.user_checked=true

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
				Helper.redirect('/volunteers/create?user=' + id)
				
			},
			clearErrorMsg(name) {
				this.form.errors.clear(name)
			},
			onSubmit() {
				if(this.isNewUser){
					this.form.user.name=this.form.user.profile.fullname

					this.formSubmitting=true
					this.$refs.userinputs.checkUser()
				}else{
					this.submitForm()
				}
				
			},
			submitForm() {
				
				let store=Volunteer.store(this.form)
					.then(user => {
						Helper.BusEmitOK()
						this.formSubmitting=false

						Helper.redirect('/volunteers')
													
					})
					.catch(error => {
						this.formSubmitting=false
						Helper.BusEmitError(error,'存檔失敗') 
						
						
					})
			},
			
			
			
		




		},

    }
</script>