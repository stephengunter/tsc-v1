<template>
  <ul class="nav navbar-nav" style="font-weight:bold;">
      

      <li v-for="item in menus" v-bind:class="{ active: item.active }">
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
            
           
        }
        
    }
</script>