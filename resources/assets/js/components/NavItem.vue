<template>
    <li :class="{ 'active': item.active, 'dropdown': hasSubItems }">
        <a v-if="hasSubItems" :href="item.path" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle">
            {{ item.text }} 
            <span class="caret"></span>
        </a> 
        <a v-else :href="item.path" >
            {{ item.text }} 
        </a>  
        <ul v-if="hasSubItems" class="dropdown-menu">
            <nav-item v-show="!hideItem(item)"  v-for="(item,index) in item.items" :key="index" 
                :item="item" >
            
            </nav-item>
            
        </ul>
    </li>
    
</template>


<script>
    export default {
        name: 'NavItem',
        props: {
            item: {
              type: Object,
              default: null
            },
        },
        data() {
            return {
              
               
            }
        },
        computed:{
            hasSubItems(){
                if(!this.item) return false
                if(!this.item.items) return false
                return  this.item.items.length > 0
            }
        },
        beforeMount(){
          
        },
        methods: {
            getClass(item){
                let style=''
                if(item.active) style='active'
                if(this.hasSubItems)  return 'dropdown'
                return 'active'
            },
            hideItem(item){
                if(item.hide) return true
                return false
            },
           
        }
        
    }
</script>