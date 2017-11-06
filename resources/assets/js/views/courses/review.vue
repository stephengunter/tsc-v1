<template>
<div>
    <course-selector ref="selector" :source_url="source_url"
        :params="params" :options="filter_options" :title_text="title"
        @details="onDetails" @submit="submit">
        <span slot="btn">
            <span  class="glyphicon glyphicon-ok" aria-hidden="true"></span> 
            審核通過
        </span>
    </course-selector>

    

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
    import CourseSelector from '../../components/course/selector.vue'
    import Show from '../../components/course/show.vue'
    import SignupInfoComponent from '../../components/course/signupinfo/view.vue'
    import ClasstimeComponent from '../../components/classtime/classtime.vue'
    export default {
        name: 'CourseReview',  
        components: {
            'course-selector':CourseSelector,
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
                title:'課程審核',
                source_url:CourseReview.source(),
                params:{
                    reviewed: 0,
                    center : 0,
                    parent:0
                },

                filter_options:{
                    centerOptions:  this.center_options
                },

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
        watch: {
            version() {
               this.init()
               this.listSettings.version +=1
            },
        
        },
        beforeMount() {
            
            this.params.center=this.center_options[0].value
            this.init()
        },
        methods: {
            init(){

                this.selected=0

                this.modalSettings.show=false
                this.reviewEditor.show=false
                              
            },
            refresh(){
                this.$refs.selector.refresh()
            },
            onDetails(id){
                this.selected=id
                this.modalSettings.show=true
               
            },
            setSelectedCourse(course){
                this.selectedCourse=course
            },
            submit(course_ids){
               
                let save = CourseReview.store(course_ids)
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