<template>
   <tr>
        
        <td>
            <a href="#" @click.prevent="selected(category.id)">{{ category.name}}</a> 
        </td>
        <td>{{ category.code }}</td>
        <td v-html="getType(category)"></td> 
        <td v-html="$options.filters.showIcon(category.icon)"></td> 
        <td v-html="$options.filters.activeLabel(category.active)"></td> 

        <td v-show="!editting_order" v-text="category.order">
                            
        </td> 
        <td v-show="editting_order">
            <input @keydown="clearErrorMsg" type="text" name="category.order" class="form-control" v-model="category.order">
  
            <small class="text-danger" v-text="category.error"></small>
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
            index: {
               type: Number,
               default: -1
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
            editting_order:{
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
               return Category.getType(category)
            },
            selected(id){
             
                this.$emit('selected',id)
            },
            clearErrorMsg(){
                this.$emit('clear-error', this.index)
            },
           

        },
        
    }
</script>