<template>
    <ul class="nav navbar-nav" style="font-weight:bold;">
        
        <!-- <li class="dropdown">
            <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle">
                 stephen 
                <span class="caret"></span>
            </a> 
            <ul class="dropdown-menu">
                <li>
                    <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                          登出
                    </a>
                </li>
                <li>
                     <a href="/change-password">變更密碼</a>
                </li>
            </ul>
        </li> -->
        <nav-item v-show="!hideItem(item)"  v-for="(item,index) in menus" :key="index" 
          :item="item" >
        
        </nav-item>
    </ul>
</template>


<script>
    export default {
        name: 'MainNav',
        props: {
            menus: {
              type: Array,
              default: []
            },
        },
        data() {
            return {
              
               
            }
        },
        beforeMount(){
          
        },
        methods: {
            hideItem(item){
                if(item.hide) return true
                return false
            },
            
           
        }
        
    }
</script>