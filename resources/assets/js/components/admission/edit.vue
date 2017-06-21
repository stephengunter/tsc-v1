<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">           
             <span class="panel-title">
                   <h4 v-html="title"></h4>                  
             </span>           
        </div>
        <div v-if="loaded" class="panel-body">
             <table v-show="hasData || creating" class="table table-striped" style="width: 99%;">
                <thead> 
                    <tr> 
                        <th v-for="item in thead"></th> 
                    </tr> 
                </thead>
                <tbody> 
                    <tr  v-for="item in admission.admits"></tr>
                </tbody>
            
            </table>
        </div>
    </div>
</div>
  


  


</template>

<script>
    import List from './list.vue'
    export default {
        name: 'EditAdmission',
        components: {
            'admit-list':List,
        },
        props: {
            course_id: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                creating:true,
                course:null,
                admission:null

            }
        },
        methods: {
            onDataLoaded(data){
                this.course=data.course
            },
          
        }, 
       
       
    }
</script>
