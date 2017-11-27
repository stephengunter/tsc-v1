<template>
<div>
    <div class="panel panel-default">
        <div v-if="show_title" class="panel-heading">           
             <span class="panel-title">
                   <h4 v-html="title"></h4> 
             </span>   
             
        </div>
        <div class="panel-heading">
            <div class="form-inline">
               
                <div class="form-group">
                    <select  v-model="params.term"    style="width:auto;" class="form-control selectWidth">
                        <option v-for="item in termOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select  v-model="params.center" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in centerOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select  v-model="params.category" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in categoryOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div class="form-group">
                    <select  v-model="params.weekday" style="width:auto;" class="form-control selectWidth">
                         <option v-for="item in weekdayOptions" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
            </div>

            <div v-if="hasSelected">
                 <button type="button" @click.prevent="submit" class="btn btn-success">確認送出</button>
                 &nbsp;&nbsp;&nbsp;
                <button class="btn btn-default" @click.prevent="cancel">取消</button>
            </div>
            <div v-else>
                {{ default_text }}
                &nbsp;&nbsp;&nbsp;
                <button class="btn btn-default" @click.prevent="cancel">取消</button>
            </div>
        </div>
    </div>
     
    <course-list v-if="ready" :search_params="params"  
        :hide_create="listSettings.hide_create" 
        :can_select="listSettings.can_select"
        :show_title="listSettings.show_title"
        :selected_ids="selectedIds"
        @selected="onSelected" @unselected="onUnSelected">
    </course-list>

</div>

</template>

<script>
    import CourseList from './list.vue'
 
    export default {
        name: 'CourseFullSelector',       
        components: {
           'course-list':CourseList
        },
        props: {
            show_title:{
               type: Boolean,
               default: false
            },
            title:{
               type: String,
               default: ''
            },
            default_text:{
               type: String,
               default: ''
            },
            selected_ids: {
              type: Array,
              default: null
            },
        },
        data() {
            return {
                ready:false,

                listSettings:{
                    hide_create:false,
                    can_select:true,
                    show_title:false
                },

                params:{
                    term:0,
                    center:0,
                    category:0,
                    weekday:0,
                },
                
                termOptions:[],
                categoryOptions:[],
                centerOptions:[],
                weekdayOptions:[],

               
                selectedIds:[],
                selectedCourses:[],

             
            }
        },
        computed: {
            hasSelected() {
                return this.selectedIds.length > 0
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                if(this.selected_ids) this.selectedIds=this.selected_ids
                
                let options=Course.indexOptions()
                options.then(data=>{
                    this.termOptions=data.termOptions
                    this.params.term=this.termOptions[0].value

                    this.centerOptions=data.centerOptions
                    this.params.center=this.centerOptions[0].value

                    this.categoryOptions=data.categoryOptions
                    let defaultItem={ text:'全部課程分類' , value:'0' }
                    this.categoryOptions.splice(0, 0, defaultItem);
                    this.params.category=this.categoryOptions[0].value

                    this.weekdayOptions=data.weekdayOptions
                    let allWeekdays={ text:'不限星期' , value:'0' }
                    this.weekdayOptions.splice(0, 0, allWeekdays);
                    this.params.weekday=this.weekdayOptions[0].value
                    
                    this.ready=true
                    
                }).catch(error=>{
                    Helper.BusEmitError(error)
                    this.ready=false
                })
             
            },
            onSelected(id,number,name){
                let index = this.selectedIds.indexOf(id)
                if (index < 0 ) {
                    this.selectedIds.push(id)
                    let course={
                        id:id,
                        number:number,
                        name:name
                    }
                    this.selectedCourses.push(course)
                }

               
            },
            onUnSelected(id){
                 let index = this.selectedIds.indexOf(id)
                 if (index > -1) {
                    this.selectedIds.splice(index, 1)
                 }
                this.$emit('selected',id)
            },
            cancel(){
                this.$emit('canceled')
            },
            submit(){
                this.$emit('submit',this.selectedIds , this.selectedCourses)
            },
            
            
        },

    }
</script>