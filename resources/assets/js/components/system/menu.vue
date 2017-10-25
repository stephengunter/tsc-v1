<template>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4 v-html="titleHtml">
                </h4>
            </span> 
        </div>  <!-- End panel-heading-->
        <div class="panel-body" >
            <div class="row">
                <div v-for="(item,index) in items" :key="index" class="col-md-6">
                    <ul style="list-style-type: square;">
                        <li> 
                            <a :href="item.path"> {{ item.text  }} </a>
                            <span v-if="hasUnReviewTeachers(item.path)" class="badge badge-pill  badge-primary">
                               {{ badges.unreviewTeachers  }}
                            </span>
                           
                        </li> 
                        
                    </ul>                 
                </div>
               
            </div>   <!-- End row-->
     
        </div><!-- End panel-body-->
    </div>
</template>

<script>
    export default {
        name: 'MenuItem',       
        props: {
            title: {
                type: String,
                default: ''
            },
            items:{
                type: Array,
                default: []
            },
            badges: {
                type: Object,
                default: null
            },
            
        },
        data() {
            return {
                titleHtml:'',
                unReviewTeachers:0
            }
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init(){
                let title=this.title.toLowerCase()
                this.titleHtml=this.getTitle(title)
               
            },
            getTitle(title) {
                let html= Helper.getIcon(title) 
                switch (title) {
                    case 'users':
                        html += ' 使用者管理'
                        break
                    case 'teachers':
                         html += ' 教師管理'
                        break
                    case 'courses':
                        html += ' 課程管理'
                         break
                    case 'students':
                        html += ' 學員管理'
                        break
                    case 'signups':
                         html += ' 報名管理'
                         break
                    case 'refunds':
                         html += ' 退費管理'
                         break
                    case 'discounts':
                         html += ' 折扣管理'
                         break
                    case 'main_settings':
                         html += ' 主要設定'
                    break
                    case 'settings':
                        html += ' 基本設定'
                         break
                    case 'admins':
                        html += ' 權限管理'
                        break
                    case 'notices':
                        html += ' 公告管理'
                    break
                    case 'reports':
                        html += ' 報表'
                    break
                }

                return html
                
            },
            
            hasUnReviewTeachers(path){
                if(path!='/teachers/review') return false
                if(!this.badges)  return false
                return this.badges.unreviewTeachers > 0
            }
        },

    }
</script>