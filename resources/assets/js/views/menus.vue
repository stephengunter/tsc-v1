<template>
    <div v-if="loaded" class="row">
        <div v-if="this.systems.signups" class="col-md-6">
            <menu-item  title="Signups" :items="this.systems.signups"></menu-item>
        </div>
        <div v-if="this.systems.refunds" class="col-md-6">
            <menu-item  title="Refunds" :items="this.systems.refunds"></menu-item>
        </div>
        <div v-if="this.systems.users" class="col-md-6">
            <menu-item title="Users" :items="this.systems.users"></menu-item>
        </div>
        <div v-if="this.systems.teachers" class="col-md-6">
            <menu-item title="Teachers" :items="this.systems.teachers"></menu-item>
        </div>
        <div v-if="this.systems.settings" class="col-md-6">
            <menu-item title="Settings" :items="this.systems.settings"></menu-item>
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
