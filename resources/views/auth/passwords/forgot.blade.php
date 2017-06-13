
@extends('layouts.master')

@section('content')
  <div v-if="success">
         
        <h3 style="line-height:2">系統已發送驗證信到您的電子信箱 <span v-text="email"><span></h3>
        <h4><em class="shine">驗證碼於24小時內有效</em>，驗證成功即可重新設定您的密碼。</h4>
       
  </div>                 
  <forgot-password v-else  @success="onSuccess"></forgot-password>
 
@endsection

@section('scripts')

  <script>
     new Vue({
        el: '#content',
        data() {
            return {
                email:'',
                success:false,
            }
        },
        beforeMount() {
             this.init()
        },
        methods: {
            init(){
                this.success=false     
            },
            onSuccess(email){
                this.email=email
                this.success=true
            },
            

        },
    

    })
  </script>


@endsection