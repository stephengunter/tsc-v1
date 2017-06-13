<template>

    <div  class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4><i class="fa fa-pencil-square-o" aria-hidden="true"></i>  課程資料</h4>
             </span>
              
            <div>
                <button v-if="course.canEdit" v-show="canEdit" @click="btnEditCilcked" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                </button>
                <button v-if="course.canDelete"  v-show="canEdit"  @click="beginDelete" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
               </button>
            </div>
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">
                    <img v-show="photo.path" :src="photo.path"  class="img-thumbnail  profile-img" alt="個人相片" >
                 
                </div>
                <div class="col-sm-3">
                  
                   
                    <label class="label-title">名稱</label>
                    <p>{{course.name}}</p>
                   
                    <label class="label-title">課程分類</label>
                    <p v-html="categoriesText(course.categories)"></p>

                     <label class="label-title">起始日期</label>
                    <p>{{course.begin_date}}</p>

                   
                    
                </div>
                <div class="col-sm-3">
                    <label class="label-title">開課中心</label>
                    <p> {{ course.center.name }}</p>

                    <label class="label-title">教師</label>
                     <p v-html="teachersText(course.teachers)"></p>

                   

                    <label class="label-title">結束日期</label>
                     <p>{{  course.end_date }}</p>

                     

                </div>
                 <div class="col-sm-3">
                    <label class="label-title">狀態</label>
                    <p v-html="$options.filters.activeLabel(course.active)">                       
                    </p>

                     <label class="label-title">週數</label>
                     <p>{{  course.weeks }}</p>

                     <label class="label-title">時數</label>
                     <p>{{  course.hours }}</p>

                    
                </div>
            </div>  <!-- End row-->

            <div class="row">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-3">
                   
                    <label class="label-title">課程編號</label>
                    <p>{{course.number}}</p>
                    
                </div>
                <div class="col-sm-3">
                    <label class="label-title">學分數</label>
                    <p>
                         {{ course.credit_count }}
                    </p> 
                    
                </div>
               
                <div class="col-sm-3">
                    
                    
                </div>
            </div> 
            <div class="row">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-6">
                   <label class="label-title">上課時間</label>
                   <p v-html="getClassTimesText(course.class_times)">
                       
                   </p>
                </div>
                <div class="col-sm-3">
                   <label class="label-title">最後更新</label>
                      <p v-if="!course.updated_by"> {{   course.updated_at|tpeTime  }}</p>
                      <p v-else>
                        <a  href="#" @click.prevent="showUpdatedBy" >
                            {{   course.updated_at|tpeTime  }}
                        </a>
                        
                      </p>
                </div>
               
            </div> 

       
        </div>  <!-- End panel-body-->

    </div>


</template>

<script>

    export default {
        name: 'ShowCourse',
        props: ['course','canEdit'],
        data() {
            return {
                photo: {
                    path: ''
                },
            }
        },
        beforeMount() {
            this.init()
        },   
        methods: { 
            init(){
                this.photo= {
                    path: ''
                };
                this.getPhoto()
            },   
            getPhoto() {
                let photo_id = this.course.photo_id
                let url = '/api/photoes/';
                if (photo_id) {
                    url += photo_id
                } else {
                    url += 'defaultCourse'
                }

                axios.get(url)
                    .then(response => {
                        this.photo = response.data.photo
                    })
                    .catch(function(error) {
                        console.log(error)
                    })
            },
            teachersText(teachers){
                 return CourseScripts.teachersText(teachers)             
            },
            categoriesText(categories){
               return CourseScripts.categoriesText(categories)    
            },
            getClassTimesText(class_times){
                return CourseScripts.getClassTimesText(class_times)             
            },
            btnEditCilcked(){
               this.$emit('beginEditCourse');
            },
            
            beginDelete(){
                this.$emit('beginDelete')
            },
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.course.updated_by)
            },
            
        }
    }
</script>