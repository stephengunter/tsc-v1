\<template>
<div v-if="loaded">
    <div class="row">
        <div v-if="keys[0]" class="col-md-6">
            <menu-item  :title="keys[0]" :items="systems[keys[0]]"></menu-item>
        </div>
        <div v-if="keys[1]" class="col-md-6">
            <menu-item  :title="keys[1]" :items="systems[keys[1]]"></menu-item>
        </div>
    </div> 
    <div v-if="keys.length > 2" class="row">
        <div v-if="keys[2]" class="col-md-6">
            <menu-item  :title="keys[2]" :items="systems[keys[2]]"></menu-item>
        </div>
        <div v-if="keys[3]" class="col-md-6">
            <menu-item  :title="keys[3]" :items="systems[keys[3]]"></menu-item>
        </div>
    </div>
    <div v-if="keys.length > 4" class="row">
        <div v-if="keys[4]" class="col-md-6">
            <menu-item  :title="keys[4]" :items="systems[keys[4]]"></menu-item>
        </div>
        <div v-if="keys[5]" class="col-md-6">
            <menu-item  :title="keys[5]" :items="systems[keys[5]]"></menu-item>
        </div>
    </div>  
    <div v-if="keys.length > 6" class="row">
        <div v-if="keys[6]" class="col-md-6">
            <menu-item  :title="keys[6]" :items="systems[keys[6]]"></menu-item>
        </div>
        <div v-if="keys[7]" class="col-md-6">
            <menu-item  :title="keys[7]" :items="systems[keys[7]]"></menu-item>
        </div>
    </div> 
    <div v-if="keys.length > 8" class="row">
        <div v-if="keys[8]" class="col-md-6">
            <menu-item  :title="keys[8]" :items="systems[keys[8]]"></menu-item>
        </div>
        <div v-if="keys[9]" class="col-md-6">
            <menu-item  :title="keys[9]" :items="systems[keys[9]]"></menu-item>
        </div>
    </div> 
 </div>   
</template>

<script>
    import MenuItem from '../components/system/menu.vue'
    

    export default {
        props:['id'],
        components: {
            'menu-item':MenuItem,
        },
        name: 'Menus',
        data() {
            return {
                loaded:false,
                systems:[],
                keys:[],
                test:'',
            }
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
               this.fetchData()
            },
            fetchData() {
                let id=this.id
                let url = '/home'
                
                axios.get(url)
                    .then(response => {
                        this.systems = response.data.model
                        this.keys=Object.keys(this.systems)
                        this.loaded = true
                    })
                    .catch(error => {
                        console.log(error)
                        this.loaded=false
                    })
            }, 
            
            
            
            
        }
    }
</script>
