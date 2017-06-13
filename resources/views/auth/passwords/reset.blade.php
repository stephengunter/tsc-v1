
@extends('layouts.master')

@section('content')
                    
  
  <div v-if="posted">
        <div v-if="success">
            <h1 class="text-success"><span class="glyphicon glyphicon-ok-sign"></span>重設密碼成功</h1>
            
            <h5 style="line-height:2">系統將在<span class="text-danger">@{{ count }} 秒</span>後重新導向至首頁
            </h5>
        </div>
        <div v-else>
             <h3 class="text-danger"><span class="glyphicon glyphicon-remove-circle"></span> 重設密碼失敗</h3>
             <h4 style="line-height:2">
                <span class="text-danger"> 可能的失敗原因：您輸入的Email錯誤 或 驗證碼已過期</span>
            </h4>
           
            
           <button @click.prevent="onTryAgain" class="btn btn-info">重啟密碼重設程序</button>
           
         
         
        </div>
  </div>
  <reset-password v-else user_id="{{ $user_id }}" token="{{ $token }}"  
     @failed="onFailed" @success="onSuccess" >
     
  </reset-password>
@endsection

@section('scripts')

  <script>
     new Vue({
        el: '#content',
        data() {
            return {
                posted:false,
                success:false,
                count:3,
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
               this.posted=false
               this.success=false
               this.count=3 
               
            },
            onSuccess(){
               this.posted=true
               this.success=true
               this.countDown()
            },
            onFailed(){
               this.posted=true
               this.success=false
            },
            onTryAgain(){
               let url='/forgot-password'
               Helper.redirect(url)
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