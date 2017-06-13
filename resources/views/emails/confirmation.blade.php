@component('mail::message')
#  {{ $user->name }} ，您好！

您申請註冊成為本站會員，請點擊以下連結按鈕完成註冊Email認證程序：



@component('mail::button', ['url' => $url])
點此完成Email認證
@endcomponent



@endcomponent
