@extends('layouts.master')

@section('content')
    <div v-if="submitted">
        <div v-if="success">
            <h3 style="line-height:2">系統已發送會員認證信到您的電子信箱 <span v-text="email"></span> </h3>
            <h4><em class="shine">驗證碼於24小時內有效</em>，驗證成功即成為本站會員。</h4>
             
        </div>
        <div v-else>
            <h3 class="text-danger">
                <span class="glyphicon glyphicon-remove-circle"></span> 重發認證信失敗
            </h3>
        </div>
       
    </div> 
    <div v-else class="alert alert-dismissable alert-warning">
       
        <h3 style="line-height:2">您的會員帳號尚未完成Email認證.</h3>
        
        <h4 style="line-height:2">請至您註冊的電子信箱 <span v-text="email"></span> 開啟我們mail給您的會員認證信來完成認證.
        </h4>

        <h4 style="line-height:2">
            <span class="text-danger"> ※您必須完成Email認證才可開通會員權限</span>
        </h4>

        
        <div class="row">
            
            <div class="col-sm-2">
                <form v-if="email"  @submit.prevent="onSubmit">
                  <button type="submit" class="btn btn-success">重發認證信</button>
                </form>
            </div>
         
        </div>
   
    </div>
    


    
     
 
@endsection

@section('scripts')


  <script>
     new Vue({
        el: '#content',
        data() {
            return {
               email:'',
               submitted:false,   
               success:false
            }
        },
        
        beforeMount() {
             @if(isset($email))
                  this.email='{{ $email }}'                 
             @endif    
            
        },
        methods: {
            onSubmit(){
               let send=this.$auth.sendConfirmationMail(this.email)
               
               send.then(response => {
                  this.submitted=true
                  this.success=true
               })
               .catch(error => {
                    this.submitted=true
                   this.success=false
               })
               
            },
            

        },
    

    })
  </script>


@endsection