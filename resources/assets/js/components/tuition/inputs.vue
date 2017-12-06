<template>

<div v-if="form">
   
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">                           
                <label>繳費方式</label>
                <select  v-model="form.tuition.pay_by" class="form-control">
                <option v-for="(item,index) in payways" :key="index" :value="item.value" v-text="item.text"></option>
                </select>
            </div>
        </div>
        <div v-if="edit_date" class="col-sm-3">
            <div class="form-group">                           
                <label>繳費日期</label>
                <div>
                    <date-picker :option="datePickerOption" :date="date" ></date-picker>
                </div>
                <input type="hidden" name="tuition.date" class="form-control" v-model="form.tuition.date"  >
                
            </div>
        </div>
        <div  class="col-sm-3">
            <div class="form-group"> 
                <label>繳費金額</label>
                <input type="text" name="tuition.amount" class="form-control" v-model="form.tuition.amount" @keydown="clearErrorMsg('tuition.amount')"  >
                <small class="text-danger" v-if="form.errors.has('tuition.amount')" v-text="form.errors.get('tuition.amount')"></small>
            </div> 
            
        </div>
        <div v-if="edit_date" class="col-sm-3">
            <div class="form-group">
                <label>備註</label>
                <input type="text" name="tuition.ps" class="form-control" v-model="form.tuition.ps"   >
            
            </div>
        </div>
        <div v-else class="col-sm-6">
            <div class="form-group">
                <label>備註</label>
                <input type="text" name="tuition.ps" class="form-control" v-model="form.tuition.ps"   >
            
            </div>
        </div>
    </div>   <!--  row   -->
  
   
</div>  
</template>


<script>
export default {
    name: 'TuitionInputs',
    props: {
        form: {
            type: Object,
            default: null
        },
        payways:{
            type:Array,
            default:null
        },  
        edit_date:{
            type:Boolean,
            default:false
        }          
    },
    data() {
        return {
            datePickerOption: Helper.datetimePickerOption(),
            date: {
					time: ''
				},
            
        }
    },
    watch:{
         
         date: {
            handler: function () {
               this.form.tuition.date=this.date.time
               
            },
            deep: true
         },
    },
    beforeMount() {
        this.init()
    },
    methods: {
        init() {
           if(this.edit_date) this.date.time=this.form.tuition.date

				
        },
        clearErrorMsg(name){
            this.$emit('clear-error',name)
        },
        
    }
}
</script>