<template> 

  <img v-show="photo.path" :src="photo.path"  :class="class_name" :alt="title" >
                 
</template>
<script>

    export default {
        name: 'PhotoImage',
        props: {
            id: {
              type: Number,
              default: 0
            },
            type:{
               type: String,
               default: 'profile'
            }
        },
        watch: {
            'id': 'init'
        }, 
        data() {
             return {
                photo: {
                    path: ''
                },
                title:'個人相片',
                class_name:"img-thumbnail  profile-img",

            }
        },
        beforeMount() {
            this.init()
        },  
        methods: {    
            init(){
                this.photo= {
                    path: ''
                }

                if(this.type!='profile'){
                    this.title=''
                }

                this.getPhoto()
            },   
            getPhoto() {
                let photo_id = this.id               
               
                if (photo_id) {
                    let show=Photo.show(photo_id)
                    show.then(data => {
                        this.photo = data.photo
                    })
                    .catch(function(error) {
                        console.log(error)
                    })

                } else {
                    this.photo  = Photo.default('profile')
                }

               
            },
           
            

            
        }
    }
</script>