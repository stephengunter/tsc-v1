<template>
  <ul class="nav navbar-nav" style="font-weight:bold;">
      

      <li v-show="hideItem(item)"  v-for="(item,index) in menus" :key="index" :class="{ active: item.active }">
         <a :href="item.path" >
              {{ item.text }}
         </a>  
      </li>
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
                if(item.hide) return false
                return true
            },
           
        }
        
    }
</script>