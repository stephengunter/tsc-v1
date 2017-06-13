
@extends('layouts.master')

@section('content')
      <div  v-if="success">
            <h1 class="text-success"><span class="glyphicon glyphicon-ok-sign"></span>變更密碼成功</h1>
            
            <h5 style="line-height:2">系統將在<span class="text-danger">@{{ count }} 秒</span>後重新導向至首頁
            </h5>
      </div>              
       <change-password v-else @canceled="onCanceled"
           @failed="onFailed" @success="onSuccess">
         
       </change-password>


   
      
       
   
@endsection

@section('scripts')

  <script>
     new Vue({
        el: '#content',
        data() {
            return {
                success:false,
                count:3,
            }
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
               
               this.success=false,
               this.count=3
               
            }, 
            onCanceled(){
               this.init()
            },
            onSuccess(){
                 this.success=true 
                 this.countDown() 
            },
            onFailed(){
               
                 this.success=false 
            },
            countDown(){
                window.setInterval(() => {
                    if (this.count > 1) {
                        this.count--;
                    } else {
                         Helper.redirect('/')
                    }
                },1000);
               
            },

        },
    

    })
  </script>


@endsection