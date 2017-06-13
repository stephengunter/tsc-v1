<template>
<div>
   
   <div id="tabSettings" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a @click="activeIndex=1" href="#term" data-toggle="tab">學期設定</a>
                    </li>
                    <li>
                        <a @click="activeIndex=2" href="#classroom" data-toggle="tab">教室管理</a>
                    </li>
                    <li>
                        <a @click="activeIndex=3" href="#title" data-toggle="tab">稱謂設定</a>
                    </li>
                    <li>
                        <a @click="activeIndex=4" href="#holiday" data-toggle="tab">假日管理</a>
                    </li>
                    <li>
                        <a @click="activeIndex=5" href="#identity" data-toggle="tab">身分管理</a>
                    </li>
                    <li>
                        <a @click="activeIndex=6" href="#discount" data-toggle="tab">折扣管理</a>
                    </li>
                </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="term">

                   <term v-if="activeIndex==1"></term>
                    
                </div>
                <div  v-if="activeIndex==2" class="tab-pane fade" id="classroom">

                   <classroom></classroom>
                    
                </div> 
                <div  v-if="activeIndex==3" class="tab-pane fade" id="title">

                   <title-view></title-view>
                    
                </div> 
                <div  v-if="activeIndex==4" class="tab-pane fade" id="holiday">

                   <holiday></holiday>
                    
                </div> 
                <div  v-if="activeIndex==5" class="tab-pane fade" id="identity">

                   <identity></identity>
                    
                </div>   
                <div  v-if="activeIndex==6" class="tab-pane fade" id="discount">

                   <discount></discount>
                    
                </div>                
            </div>
        </div>
    </div>   

</div>    
</template>
<script>
    
    import Term from '../../components/term/term.vue'
    import Classroom from '../../components/classroom/classroom.vue'
    import Title from '../../components/title/title.vue'
    import Holiday from '../../components/holiday/holiday.vue'
    import Identity from '../../components/identity/identity.vue'
    import Discount from '../../components/discount/discount.vue'
    

    export default {
         components: {
            Term,
            Classroom,
            'title-view':Title,
            Holiday,
            Identity,
            Discount
        },
        name: 'SettingsView',
        data(){
            return{
                activeIndex:1
            }
        },
        methods:{
            
            
        }
        
    }
</script>
