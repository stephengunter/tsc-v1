<template>
   <nav class="navbar navbar-default" >
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand"   style="color: #337ab7;" href="#">慈大社推課程管理系統</a>
          </div>
          <div id="navbar" v-if="username" class="navbar-collapse collapse">
            <ul class="nav navbar-nav" style="font-weight:bold;font-size:19px">
             
             
            </ul>
            <ul class="nav navbar-nav navbar-right">
            
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" 
                    role="button" aria-haspopup="true" aria-expanded="false">
                    {{ username }} <span class="caret"></span>
                  </a>
                <ul class="dropdown-menu">
                 
                  
                  <li><a href="#" @click.prevent="logout">登出</a></li>
                  <router-link tag="li" to="/change-password" >
                    <a>變更密碼</a>
                  </router-link>
                  
                
                </ul>
              </li> 
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
</nav>
</template>


<script>
    export default {
        name: 'Navbar',
        props:['username'],
        components: {
          
        },

        data() {
            return {
              
               isAuth:false,
               isOwner:false,
            }
        },
      
        beforeMount(){
          
        },
        
        methods: {
            init(){
                // this.isAuth=false
               
                // if(!this.$route.meta.forVisitors){
                //     if(!this.$route.meta.forAll){
                //         this.checkAuth()
                //     }
                 
                // }
               
            },
            logout(){
              
               // this.isAuth=false
               // this.$auth.logout()
               // this.$emit('auth',false)
               // this.$router.push('/login')
            },
           
        }
        
    }
</script>