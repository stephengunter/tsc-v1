<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <combination-select :search_params="search_params"
                @ready="onCombinationReady" >
            </combination-select>
            <div v-if="hasCourse" v-html="statusHtml()">
                
            </div>
        </div>
        
     </div>
     
    <signup-list v-if="ready" :course_id="course_id" :hide_create="!canCreate" 
        :version="version" :can_select="can_select" :for_refund="for_refund"
        @loaded="onSignupListLoaded"
        @selected="onSelected" @begin-create="onBeginCreate">
    </signup-list>

    <modal :showBtn="true"  :show.sync="confirmSettings.show" @ok="onConfirmed"  @closed="confirmSettings.show=false" ok-text="確定"
        effect="fade" width="800" :ok_text="confirmSettings.ok_text">
          <div slot="modal-header" class="modal-header modal-header-danger">
           
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="confirmSettings.show=false">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
             <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
          </div>
        <div slot="modal-body" class="modal-body">
            <h3 v-text="confirmSettings.message"> </h3>
        </div>
    </modal>

</div>

</template>

<script>
    import SignupList from '../../components/signup/list.vue'


    export default {
        name: 'SignupIndex',       
        components: {
            'signup-list':SignupList,
        },
        props: {
            version: {
              type: Number,
              default: 0
            },
            
            for_refund:{
               type: Boolean,
               default: false
            },
        },
        data() {
            return {
                ready:false,
                course_id:0,
                selectedCourse:null,
               
                can_edit:true,
                can_back:true,
                can_select:false,

                search_params:{
                    term:0,
                    center:0,
                 
                    reviewed:1
                },

                confirmSettings:{
                    show:false,
                    message:'此課程名額已滿，只能以備取身分報名。是否仍要繼續？',
                    ok_text:'繼續報名'
                },

                
             
            }
        },
        computed:{
           hasCourse(){
               if(this.selectedCourse) return true
               return false
           },
           canCreate(){
               if(this.hide_create) return false
              
               if(!this.hasCourse) return false

               return this.selectedCourse.canSignup
           }
            
        }, 
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                
             
            },
            onCombinationReady(course){
               
                this.setCourse(course)
                this.ready=true
            },
            
            setCourse(val){
               
                this.course_id=val
            },
            onSignupListLoaded(data){
                this.selectedCourse=data.course
            },
            statusHtml(){
                if(!this.hasCourse) return ''

                let html= '課程上限人數：' +  this.selectedCourse.limit  + '&nbsp;'

               return  html + CourseStatus.getSignupLabel(this.selectedCourse.status)
            },
            onConfirmed(){
                this.confirmSettings.show=false
                this.$emit('begin-create',this.course_id)
            },
            onSelected(id){
                this.$emit('selected',id)
            },
            onBeginCreate(){
                if(this.canCreate) this.onConfirmed()
                else this.confirmSettings.show=true
            }
            
            
        },

    }
</script>