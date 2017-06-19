<template>
   <tr>
        
        <td>
            <a herf="#" @click.prevent="selected(category.id)">{{ category.name}}</a> 
        </td>
        <td v-html="getType(category)"></td> 
        <td v-html="$options.filters.showIcon(category.icon)"></td> 
        <td v-html="$options.filters.activeLabel(category.active)"></td> 

        <td>
            <button @click="displayUp(category.id)" class="btn btn-default btn-xs">
                <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
            </button>
            <button @click="displayDown(category.id)" class="btn btn-default btn-xs">
                <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
            </button>
        </td>   
    </tr>                   
       
</template>
<script>
  
    export default {
        name: 'CategoryRow',
        props: {
            category: {
               type: Object,
               default: null
            },
            more:{
               type: Boolean,
               default: false
            },
            remove:{
               type: Boolean,
               default: false
            },
            select:{
               type: Boolean,
               default: false
            },
            
        },
        data() {
            return {
                thead:[],
            }
        },
        beforeMount(){
            
        },
        
        methods: {
            getType(category){
                let isPublic=Helper.tryParseInt(category.public)
                if(isPublic) return '<span class="label label-warning"> 置頂 </span>'
                    return ''
            },
            selected(id){
                alert(id)
                this.$emit('selected',id)
            },
            displayUp(id){
                this.$emit('display-up',id)
            },
            displayDown(id){
                this.$emit('display-down',id)
            }

        },
        
    }
</script>