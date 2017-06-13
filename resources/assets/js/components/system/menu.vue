<template>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4 v-html="getTitle()">
                </h4>
            </span> 
        </div>  <!-- End panel-heading-->
        <div class="panel-body" style="font-size: 0.85em;">
            <div class="row">
                <div v-for="item in items" class="col-md-6">
                    <ul style="list-style-type: square;">
                        <li> 
                            <a :href="item.path"> {{ item.text  }} </a>
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
        props:['title','items'],
        
        methods: {
            getTitle() {
                let html= Helper.getIcon(this.title) 
                switch (this.title) {
                    case 'Users':
                        html += ' 使用者管理'
                        break
                    case 'Teachers':
                         html += ' 教師管理'
                        break
                    case 'Courses':
                        html += ' 課程管理'
                         break;
                    case 'Signups':
                         html += ' 報名管理'
                         break
                    case 'Refunds':
                         html += ' 退費管理'
                         break
                    case 'Settings':
                        html += ' 基本設定'
                         break
                }

                return html
            

                
            },
        },

    }
</script>