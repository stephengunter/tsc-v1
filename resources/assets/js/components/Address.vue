<template>
<div>
    <form @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        <div class="form-inline">

            <select :disabled="isReadOnly"  v-model="address.city_id" @change="loadDistricts" style="width:auto;" class="form-control selectWidth">
                <option v-for="city in cities" :value="city.id" v-text="city.name" >
                </option>
            </select>
            <select :disabled="isReadOnly"  v-model="address.district_id" style="width:auto;" class="form-control selectWidth">
                <option v-for="d in districts" :value="d.id" v-text="d.name">
                </option>
            </select>
            <input :readonly="isReadOnly" type="text"  name="address.streetAddress" v-model="address.streetAddress" style="width:50%" class="form-control" >
          <small class="text-danger" v-if="form.errors.has('address.streetAddress')" v-text="form.errors.get('address.streetAddress')"></small>
         &nbsp;
            <button v-show="isReadOnly" class="btn btn-primary btn-xs" @click.prevent="beginEdit">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </button>
            <button v-show="!isReadOnly" class="btn btn-success btn-xs" @click.prevent="saveAddress">
             
              <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
            </button>  

            <button v-show="!isReadOnly" class="btn btn-default btn-xs" @click.prevent="cancelEdit">
                <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>               
            </button> 

            <button v-show="showDelete()" class="btn btn-danger btn-xs" @click.prevent="btnDeleteClicked">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
           
            
         
        </div>
    </form>

     <modal :showBtn="true"  :show.sync="showConfirm" @ok="deleteAddress"  @closed="closeConfirm" ok-text="確定"
        effect="fade" width="800">
          <div slot="modal-header" class="modal-header modal-header-danger">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="closeConfirm">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
             <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <h3 v-text="confirmMsg"> </h3>
        </div>
     </modal>

  </div>  
</template>


<script>
    export default {
        name: 'EditAddress',
        props: {
            id: {
              type: Number,
              default: 0
            },
            name:{
               type: String,
               default: ''
            }
        },
        data() {
            return {

                address: {
                    city_id: 0,
                    district_id: 0
                },
                cities: [],
                districts: [],
                form: new Form(),

                showConfirm:false,
                confirmMsg:'',
                isReadOnly:true,

            }
        },
        mounted() {
           this.init(this.id);
        },
        methods: {
            init(id){
                this.isReadOnly=true;                
                if (id) {
                    this.fetchData(this.id)
                }else {
                    this.address= {
                        id:0,
                        city_id: 0,
                        district_id: 0
                    }
                    this.cities =[
                        { id:0 , name:'-------' },
                    ];
                     this.districts =[
                        { id:0 , name:'-------' },
                    ];
                }
            },
            fetchData(id) {
                let getData = null
                if (id > 0) {
                    getData = Address.edit(id)          
                } else {
                  getData = Address.create() 
                }
                getData.then(data => {
                            this.address = data.address
                            this.cities = data.cities
                            this.districts = data.districts
                            if (!this.address.district_id) {
                                this.address.district_id = this.districts[0].id
                            }
                        }).catch(error=> {
                            Helper.BusEmitError(error)
                        })
               
            },
            loadDistricts() {
                let city=this.address.city_id
                let getDistricts=Address.getDistricts(city)
                getDistricts.then(data => {
                             this.districts = data.districtList
                             this.address.district_id = this.districts[0].id
                        }).catch(error=> {
                            Helper.BusEmitError(error)
                        })
            },
            showDelete(){
                return (this.isReadOnly && this.id>0 );
            },
            beginEdit(){
                this.$emit('editting')
                this.fetchData(this.address.id);
                this.isReadOnly=false
            },
            cancelEdit(){
                this.clearErrorMsg('address.streetAddress')
                if(this.address.id>0){
                     this.isReadOnly=true
                 }else{
                     this.init(this.address.id)
                 }
                 this.$emit('canceled')
            },
            onSubmit() {
                this.saveAddress()
            },
            btnDeleteClicked(){
                this.confirmMsg='確定要刪除' + this.name + '嗎？'
                this.showConfirm=true
               
            },
            closeConfirm(){
                this.showConfirm=false
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            deleteAddress(){
                    let id= this.address.id 
                    let remove=Address.delete(id)
                    remove
                    .then(result => {
                        this.init(0);
                        this.closeConfirm();

                        this.$emit('deleted')
                    })
                    .catch(error => {
                       Helper.BusEmitError(error,'刪除失敗')                           
                    })
            },
            saveAddress() {
                this.form = new Form({
                    address: this.address
                })
                let save=null
                let id = parseInt(this.address.id)
                if (id > 0) {
                    save=Address.update(this.form , id)
                }else{
                    save=Address.store(this.form)
                }
                save.then(address => {
                       this.address = address
                       this.isReadOnly=true;
                       this.$emit('saved', address)
                    })
                    .catch(error => {
                        Helper.BusEmitError(error,'存檔失敗')
                    })
                    
            }
        },

    }
</script>
