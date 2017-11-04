<template>
<div>
    
    <div class="panel panel-default">
        <div  class="panel-heading">
            <div class="panel-title">
                <h4 v-html="title"></h4>  
                            
            </div>
            <options-filter :params="params" :parent_course="parentCourse"
                :options="filter_options"
                @ready="ready=true"  @param-changed="onParamChanged"
                @clear-parent="clearParent" >
            </options-filter>  
            
            <div>
                <button :disabled="!canSubmit" @click.prevent="submit" class="btn btn-success" >
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 審核通過
                </button>
                
            </div>
        </div>
    </div>

    <course-list v-if="ready" :source_url="listSettings.source_url" :no_page="listSettings.no_page" 
        :show_title="listSettings.show_title" :no_search="listSettings.no_search"
        :can_edit_number="listSettings.canEditNumber"  :search_params="params"  
        :hide_create="listSettings.hide_create" :version="listSettings.version"  
        :multi_select="listSettings.multi_select" :selected_ids="checked_ids"
        @data-loaded="onCoursesLoaded"
        @details="onDetails"
        @checked="onChecked" @unchecked="onUnChecked"
        @group-selected="onGroupSelected" 
        @checkall="checkAll"   @uncheckall="unCheckAll" > 
    </course-list> 

    <modal :width="modalSettings.width" :showbtn="modalSettings.showBtn"  :show.sync="modalSettings.show" 
     :large="modalSettings.large"  @closed="onCloseModal" effect="fade" >   
    
        <div slot="modal-header" class="modal-header">
       
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onCloseModal">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
            
        </div>
        <div slot="modal-body" class="modal-body">
            <show v-if="selected"  :id="selected" :hide_delete="showSettings.hide_delete"
                :can_back="showSettings.can_back" :can_edit="showSettings.can_edit"
                @loaded="setSelectedCourse" @edit-review="onEditReview"> 
            </show>
            <signup-info v-if="selectedCourse" :course="selectedCourse" 
               :can_edit="showSettings.can_edit">
            </signup-info> 
            <classtime v-if="selectedCourse"
                :classtime_list="selectedCourse.classtimes"   :can_edit="showSettings.can_edit"  >          
            </classtime>
        </div>
    </modal>

    <review-editor :showing="reviewEditor.show" :reviewed="reviewEditor.reviewed"
      @close="reviewEditor.show=false" @save="updateReview">
    </review-editor>

</div>    
</template>
    
<script>
    import CourseList from '../../components/course/list.vue'
    import Show from '../../components/course/show.vue'
    import SignupInfoComponent from '../../components/course/signupinfo/view.vue'
    import ClasstimeComponent from '../../components/classtime/classtime.vue'
    export default {
        name: 'CourseReview',  
        components: {
            'course-list':CourseList,
            'show':Show,
            'signup-info' : SignupInfoComponent,    
            'classtime' : ClasstimeComponent,      
        },
        props: {
            center_options:{
                type: Array,
                default: []
            },
            version: {
                type: Number,
                default: 0
            },
            
        },
        data() {
            return {
                
                ready:false,
                title:Helper.getIcon('Courses')  + '  課程審核',
                thead:Course.getThead(),

                params:{
                    reviewed: 0,
                    center : 0,
                    parent:0
                },

                filter_options:{
                    centerOptions:  this.center_options
                },

                listSettings:{
                    version:0,
                    source_url:CourseReview.source(),
                    show_title:false,
                    no_search:true,
                    no_page:true,
                    canEditNumber:true,
                    multi_select:true,
                    hide_create:true,
                    
                },

                parentCourse:null,

                checked_ids:[],

                courseList:[],

                modalSettings:{
                    width:1200,
                    large:true,
                    show:false,
                    showBtn:false

                },
                showSettings:{
                    can_edit:false,
                    can_back:false,
                    hide_delete:true

                },

                reviewEditor:{
                    show:false,
                    reviewed:false
                },

                selected:0,
                selectedCourse:null
                
            }
        },
        computed: {
            
            canSubmit() {
                return  this.checked_ids.length > 0
            }
            
        },
        watch: {
            version() {
               this.init()
               this.listSettings.version +=1
            },
        
        },
        beforeMount() {
            this.ready=false
            this.params.center=this.center_options[0].value
            this.init()
        },
        methods: {
            init(){

                this.selected=0
                this.checked_ids=[]
                this.courseList=[]

                this.modalSettings.show=false
                this.reviewEditor.show=false
                              
            },
            refresh(){
              
                this.listSettings.version+=1
            },
            onCoursesLoaded(data){
                this.courseList=data.model.data
                this.parentCourse = data.parentCourse
               
            },
            onGroupSelected(id){
                this.params.parent=id  
            },
            clearParent(params){
                this.parentCourse=null
                this.onParamChanged(params)
            },
            onParamChanged(params){
                for(let property in params){
                    this.params[property]=params[property]
                } 
                this.unCheckAll()
            },
            isGroup(course){
                return Helper.isTrue(course.group)
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
                if(!this.courseList)  return false
                this.courseList.forEach( course => {
                        this.onChecked(course.id)
                })
            },
            unCheckAll(){
                
                this.checked_ids=[]
            },
            onDetails(id){
                this.selected=id
                this.modalSettings.show=true
               
            },
            setSelectedCourse(course){
                this.selectedCourse=course
            },
            submit(){
               
                let save = CourseReview.store(this.checked_ids)
                save.then(data => {
                        Helper.BusEmitOK('存檔成功')
                        this.init()
                        this.refresh()
                    }).catch( error => {
                        Helper.BusEmitError(error,'存檔失敗')           
                    })
            },
            onCloseModal(){
                this.modalSettings.show=false
            },
            onEditReview(){
                this.reviewEditor.show=true     
            },
            updateReview(val){
                let id = this.selected 
                let review= val
                let save= CourseReview.update(id,review)

                save.then(course => {
                    
                    Helper.BusEmitOK('存檔成功')
                    this.init()
                    this.refresh()
                   
                })
                .catch(error => {
                    Helper.BusEmitError(error,'存檔失敗')
                    this.showReviewEditor=false   
                })
            }
            
            
        },

    }
</script>