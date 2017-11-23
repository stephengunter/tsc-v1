<template>
    <tr v-if="readOnly" >
        
        <td v-text="discount.name"></td> 
        <td v-html="$options.filters.okSign(discount.all_courses)"></td> 
        <td v-text="pointText(discount.points_one)"></td> 
        <td v-text="pointText(discount.points_two)"></td> 
        
        <td v-html="$options.filters.activeLabel(discount.active)" ></td>  
        <td v-if="editting_order" >
            <input @keydown="clearErrorMsg" type="text" name="discount.order" class="form-control" v-model="discount.order">
    
            <small class="text-danger" v-text="discount.error"></small>
        </td>
        <td v-else v-text="discount.order"> </td>
        <td> 
            <a v-if="discount.updated_by"  href="#" @click.prevent="showUpdatedBy" >
            {{   discount.updated_at|tpeTime  }}
            </a>
            <span v-else>{{   discount.updated_at|tpeTime  }}</span>
            

        </td>  
        <td>
            <button v-if="can_edit" class="btn btn-primary btn-xs" @click="onBeginEdit">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </button>  
            
        </td> 
    </tr>
    <tr v-else>
        
        <td>
            <textarea rows="5" cols="50" name="discount.name" class="form-control" v-model="form.discount.name"> </textarea>
            <small class="text-danger" v-if="form.errors.has('discount.name')" v-text="form.errors.get('discount.name')"></small>
        </td>
        <td>
            <toggle :items="boolOptions"   :default_val="form.discount.all_courses" @selected="setAllCourses"></toggle>
        </td>
        <td>
            <select   v-model="form.discount.points_one"  class="form-control" >
                <option v-for="(item,index) in pointOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
        </td>
        <td>
           <select   v-model="form.discount.points_two"  class="form-control" >
                <option v-for="(item,index) in pointOptions" :key="index" :value="item.value" v-text="item.text"></option>
            </select>
        </td>
        <td>&nbsp;</td>
           
        <td>&nbsp;</td>
            
        
        <td>
            &nbsp;
        </td>
        <td>
            
            <button @click.prevent="onSubmit"  class="btn btn-success btn-xs">
                <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
            </button>  
            <button  class="btn btn-default btn-xs" @click.prevent="onCancelEdit">
                <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
            </button>
        </td> 
    </tr>  
</template>


<script>
export default {
    name: "EditDiscount",
    props: {
        index: {
            type: Number,
            default: -1
        },
        discount: {
            type: Object,
            default: null
        },
        can_edit: {
            type: Boolean,
            default: true
        },
        editting_order: {
            type: Boolean,
            default: false
        },
        center_id: {
            type: Number,
            default: 0
        },
    },

    data() {
        return {
            readOnly: true,

            loaded: false,
            form: new Form({
                discount:{}
            }),
            identityOptions: [],
            pointOptions: [],

            boolOptions: Helper.boolOptions(),
        };
    },
    computed:{
        isCreate(){
            return   this.getId() < 1
        },
        
    },
    beforeMount() {
        this.init();
    },
    methods: {
        getId() {
            if (this.discount) return Helper.tryParseInt(this.discount.id);
            return 0;
        },
        init() {
            if (this.discount) {
                this.readOnly = true;
            }else {
                this.loaded = false;
                this.readOnly = false;
                this.fetchData();
            }
        },
        fetchData() {
            let getData = null;
            let id = this.getId();
            if (id) {
                getData = Discount.edit(id);
            } else {
                getData = Discount.create();
            }
            
            getData.then(data => {
                    if(!id) data.discount.center_id=this.center_id
                    this.form = new Form({
                        discount: data.discount
                    })
                    this.identityOptions = data.identityOptions
                    this.pointOptions=Helper.numberOptions(0, 95, true)
                })
                .catch(error => {
                    Helper.BusEmitError(error);
                });
        },
        pointText(points) {
            return Discount.pointText(points);
        },
        
        onBeginEdit() {
            this.$emit('begin-edit')
            this.fetchData()
            this.readOnly = false
        },
        onCancelEdit() {
            if(!this.isCreate)  this.readOnly = true
            
            this.$emit("canceled");
        },
        setAllCourses(val){
            this.form.discount.all_courses=val
        },
        clearErrorMsg(name) {
            this.form.errors.clear(name);
        },
        onSubmit() {
            this.submitForm();
        },
        submitForm() {
            let save = null;
            let id = this.getId();
            if (id) {
                save = Discount.update(this.form, id);
            } else {
                save = Discount.store(this.form);
            }

            
            save.then(result => {
                Helper.BusEmitOK();
                this.readOnly = true;

                this.$emit("saved");
            })
            .catch(error => {
                Helper.BusEmitError(error, "存檔失敗");
            });
        },
        clearErrorMsg() {
            this.$emit("clear-error", this.index);
        }
    }
}
</script>
