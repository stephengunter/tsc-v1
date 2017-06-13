<template>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4>
                    
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
        name: 'UsersMenu',
        props:['key','items'],
        
        methods: {
            init() {

                
            },

        },

    }
</script>