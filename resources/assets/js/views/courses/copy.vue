<template>
<div>
    <course-selector ref="selector"  :params="params"  :title_text="title"
        @details="onDetails" @submit="onSubmit">
        <span slot="btn">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
            開始複製
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

    <modal :width="confirmSettings.width" :showbtn="confirmSettings.showBtn"  :show.sync="confirmSettings.show" 
     :large="confirmSettings.large" @ok="submit"  @closed="onCloseConfirm" effect="fade" >   
    
        <div slot="modal-header" class="modal-header">
            <h4>請指定複製的目標</h4>
        </div>
       
        <div slot="modal-body" class="modal-body">
            <div class="form-inline">
                <div   class="form-group">
                    <select  v-model="form.copy.term"    style="width:auto;" class="form-control selectWidth">
                        <option v-for="(item,index) in term_options" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div>
                <div  class="form-group">
                    <select  v-model="form.copy.center" style="width:auto;" class="form-control selectWidth">
                        <option v-for="(item,index) in center_options" :key="index" :value="item.value" v-text="item.text"></option>
                    </select>
                </div> 
            </div>
        </div>
    </modal>

</div>    
</template>
    
<script>
    import CourseSelector from '../../components/course/selector.vue'
    import Show from '../../components/course/show.vue'
    import SignupInfoComponent from '../../components/course/signupinfo/view.vue'
    import ClasstimeComponent from '../../components/classtime/classtime.vue'
    export default {
        name: 'CourseCopy',  
        components: {
            'course-selector':CourseSelector,
            'show':Show,
            'signup-info' : SignupInfoComponent,    
            'classtime' : ClasstimeComponent,      
        },
        props: {
            term_options:{
                type: Array,
                default: []
            },
            center_options:{
                type: Array,
                default: []
            },
            
            
        },
        data() {
            return {
                title:'請選擇您要複製的課程',

                params:{
                    term:0,
                    center : 0,
                    category:0,
                    parent:-1
                },

                modalSettings:{
                    width:1200,
                    large:true,
                    show:false,
                    showBtn:false

                },

                confirmSettings:{
                    width:600,
                    large:false,
                    show:false,
                    showBtn:true

                },
                showSettings:{
                    can_edit:false,
                    can_back:false,
                    hide_delete:true

                },

                
                selected:0,
                selectedCourse:null,

                form : new Form({
                    copy:{
                        term: 0,
                        center: 0,
                        course_ids: []
                    }
                }),
                
            }
        },
        
        beforeMount() {
            this.form.copy.term=this.term_options[0].value
            this.form.copy.center=this.center_options[0].value
            this.init()
        },
        methods: {
            init(){
                this.form.copy.course_ids=[]
                this.selected=0

                this.modalSettings.show=false
                this.confirmSettings.show=false     
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
            onSubmit(course_ids){
                this.form.copy.course_ids=course_ids
                this.confirmSettings.show=true
            },
            submit(course_ids){
                let save = CourseImport.copy(this.form)
                save.then(data => {
                        Helper.BusEmitOK('存檔成功')
                       
                        this.init()
                        this.$emit('imported')
                        
                    }).catch( error => {
                        Helper.BusEmitError(error,'存檔失敗')           
                    })
            },
            onCloseModal(){
                this.modalSettings.show=false
            },
            onCloseConfirm(){
                this.confirmSettings.show=false
            }
            
            
            
            
        },

    }
</script>