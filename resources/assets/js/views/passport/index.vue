<template>
 <div>
   
   <div id="tabSettings" class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a @click="activeIndex=1" href="#authorizedClients" data-toggle="tab">AuthorizedClients</a>
                    </li>
                    <li>
                        <a @click="activeIndex=2" href="#clients" data-toggle="tab">Clients</a>
                    </li>
                    <li>
                        <a @click="activeIndex=3" href="#personalAccessTokens" data-toggle="tab">PersonalAccessTokens</a>
                    </li>
                    
                </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="authorizedClients">
                  
                   <authorized-clients v-if="activeIndex==1"></authorized-clients>
                    
                </div>
                <div  v-if="activeIndex==2" class="tab-pane fade" id="clients">

                   <clients></clients>
                    
                </div> 
                <div  v-if="activeIndex==3" class="tab-pane fade" id="personalAccessTokens">

                   <personal-access-tokens></personal-access-tokens>
                    
                </div> 
                              
            </div>
        </div>
    </div>   

</div>     
</template>


<script>
    import AuthorizedClients from '../../components/passport/AuthorizedClients.vue'
    import Clients from '../../components/passport/Clients.vue'
    import PersonalAccessTokens from '../../components/passport/PersonalAccessTokens.vue'

     export default {
        components: {
            'authorized-clients':AuthorizedClients,
            'clients':Clients,
            'personal-access-tokens':PersonalAccessTokens
        },
        name: 'SettingsView',
        data(){
            return{
                activeIndex:1
            }
        },
     }
</script>