@extends('layouts.master')

@section('content')

            
   <div v-if="confirmed">
       <h1 class="text-success"><span class="glyphicon glyphicon-ok-sign"></span>您已通過Email驗證. 歡迎您成為本站會員!</h1>

        <h5 style="line-height:2">
            本視窗將在 <span class="text-danger" v-text="seconds"></span> 後重新導向至登入頁面
        </h5>
        
    </div>
    <div v-else>
         <h3 class="text-danger"><span class="glyphicon glyphicon-remove-circle"></span> 驗證失敗</h3>
    </div>
@endsection

@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               confirmed:false,
               count:3,               
            }
        },
        computed:{
            seconds(){
                
                return this.count + ' 秒'
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                @if(isset($confirmed)) 
                  this.confirmed=true
                  this.countDown()
                @endif    
            },
            countDown(){
                window.setInterval(() => {
                    if (this.count > 1) {
                        this.count--;
                    } else {
                        Helper.redirect('/login')
                    }
                },1000);
               
            },
            

        },
    

    })
  </script>


@endsection