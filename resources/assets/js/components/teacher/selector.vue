<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div  class="panel-title">
                <options-filter :params="params" 
                    :options="options"
                    @ready="onFilterReady"  @param-changed="onParamChanged" >
                    
                </options-filter>
            </div>
            <div>
                
                <button :disabled="!canSubmit" @click.prevent="submit" class="btn btn-success" >
                    <slot  name="btn">
                        確認送出
                    </slot>   
                </button>
               
                
            </div>
        </div>
    </div>
    <teacher-list v-if="ready" :source_url="listSettings.source_url" :no_page="listSettings.no_page" 
        :show_title="listSettings.show_title" :title_text="listSettings.title_text"
        :no_search="listSettings.no_search" :details_link="details_link"
        :can_edit_number="listSettings.canEditNumber"  :search_params="params"  
        :hide_create="listSettings.hide_create" :version="listSettings.version"  
        :multi_select="listSettings.multi_select" :selected_ids="checked_ids"
        @data-loaded="onTeachersLoaded"
        @details="onDetails"
        @checked="onChecked"   @unchecked="onUnChecked"
        @checkall="checkAll"   @uncheckall="unCheckAll" > 
    </teacher-list> 
</div>    
</template>



<script>
    import TeacherList from './list.vue'
    export default {
        name: 'TeacherSelector',
        components: {
            'teacher-list':TeacherList                
        },
        props: {
            source_url:{
                type: String,
                default: ''
            },
            params: {
                type: Object,
                default: null
            },
            options: {
                type: Object,
                default: null
            },
            title_text:{
                type: String,
                default: ''
            },
            details_link:{
               type: Boolean,
               default: true
            },
            
        },
        beforeMount() {
            this.ready=false
            
            this.init()
        },
        computed:{
            canSubmit() {
                return  this.checked_ids.length > 0
            }
        }, 
        data() {
            return {
                
                ready:false,        
                
                teacherList:[],

                listSettings:{
                    version:0,
                    source_url:'',
                    show_title:true,
                    title_text:this.title_text,
                    no_search:true,
                    no_page:true,
                    canEditNumber:false,
                    multi_select:true,
                    hide_create:true,
                    
                },

                checked_ids:[],
            }
        },
        methods: {
            init(){
                if(this.source_url){
                    this.listSettings.source_url=this.source_url
                }else{
                    this.listSettings.source_url=Teacher.source()
                } 

                this.checked_ids=[]
                this.teacherList=[]
            },
            onFilterReady(params){
                for(let property in params){
                    this.params[property]=params[property]
                }

                this.ready=true
                
            },
            refresh(){
                this.checked_ids=[]
                this.teacherList=[]
                this.listSettings.version+=1
            },
            onTeachersLoaded(data){
                this.teacherList=data.model.data
            },
            onDetails(id){
                this.$emit('details',id);                
            },
            
            
            onParamChanged(params){
                for(let property in params){
                    this.params[property]=params[property]
                } 
                this.unCheckAll()
            },
            beenChecked(id){
                return this.checked_ids.includes(id)
            },
            onChecked(id){
                
                if(!this.beenChecked(id))  this.checked_ids.push(id) 
            },
            onUnChecked(id){
                 
                let index= this.checked_ids.indexOf(id)
                if(index >= 0)  this.checked_ids.splice(index, 1) 
                
            },
            checkAll(){
                if(!this.teacherList)  return false
                this.teacherList.forEach( teacher => {
                     this.onChecked(teacher.user_id)
                })
            },
            unCheckAll(){
                
                this.checked_ids=[]
            },
            submit(){
                this.$emit('submit',this.checked_ids);               
            }
        }
     }
</script>