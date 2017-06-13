<template>
<div v-if="courseLoaded">
 
   <show-course   v-if="isReadOnly" @beginEditCourse="beginEdit" @beginDelete="beginDelete"
   :course="course"  :canEdit="canEdit">
   </show-course>

   <edit-course v-if="!isReadOnly" @saved="onCourseSaved" @photoChanged="onPhotoChanged" @endEditCourse="endEdit"
    :id="course.id" :canEdit="canEdit">        
    </edit-course>

    <modal :showbtn="true"  :show.sync="showConfirm" @ok="deleteCourse"  @closed="closeConfirm" ok-text="確定"
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
    import ShowCourse from '../../components/course/show-course.vue'
    import EditCourse from '../../components/course/edit-course.vue'
 
    export default {
        props: ['id', 'version'],
        components: {
            'show-course':ShowCourse,  
            'edit-course':EditCourse,  
            Modal
        },
        name: 'Course',


        data() {
            return {
                canEdit:false,
                course:{
                    id:0
                },
                isReadOnly:true,
                courseLoaded:false,

                showConfirm:false,
                confirmMsg:''
            }
        },  
        beforeMount(){
            this.init()
        },
        watch: {
            'id': 'init',
            'version': 'init',
        },
        methods: {
            init() {
               this.canEdit=false
               this.course={}
               this.isReadOnly=true
               this.courseLoaded=false

               this.showConfirm=false
               this.confirmMsg=''

               this.fetchData()
            },
            fetchData() {
                let url = '/api/courses/' + this.id;
                axios.get(url)
                    .then(response => {
                        this.course = response.data.course
                        this.courseLoaded=true
                        this.canEdit=this.course.canEdit
                        this.$emit('courseLoaded',this.course);
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },    
            beginEdit() {
                 this.isReadOnly=false
            },
            endEdit(){
                 this.isReadOnly=true
            },
            onCourseSaved(){
                this.init()
            },
            onPhotoChanged(photoId){
               this.init()
               
            },
            beginDelete(){
                let refreshToken=this.$auth.refreshToken()
                refreshToken.then(() => {
                    this.confirmMsg='確定要刪除此課程嗎？'
                    this.showConfirm=true
                }).catch(error => {
                     this.$auth.logout()
                     Bus.$emit('login')
                })
                
            },
            closeConfirm(){
                  this.showConfirm=false
            },
            deleteCourse(){
                let url = '/api/courses/' + this.id 
                let form=new Form()
                form.delete(url)
                .then(result => {
                    Helper.BusEmitOK('刪除成功')
                   
                    this.$emit('deleted')
                    this.closeConfirm();
                })
                .catch(error => {
                                           
                    Helper.BusEmitError('刪除失敗')

                    this.closeConfirm();
                       
                })
            },
            
        }
    }
</script>
