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
                let title=this.title.toLowerCase()
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
                         break;
                    case 'signups':
                         html += ' 報名管理'
                         break
                    case 'refunds':
                         html += ' 退費管理'
                         break
                    case 'discounts':
                         html += ' 折扣管理'
                         break
                    case 'settings':
                        html += ' 基本設定'
                         break
                }

                return html
            

                
            },
        },

    }
</script>