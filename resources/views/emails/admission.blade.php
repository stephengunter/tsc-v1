@component('mail::message')
<style>
    table {
       font-family: '微軟正黑體', 'Lato', Helvetica, Arial, sans-serif;
    }
    th, td {
       height: 35px;   
       vertical-align: center;   
    }
    .highlight {
        color:red;
        padding:0 4px;
    }
    .back-green-th{
        text-align:right;
        background:#90c32c;
        padding:2px 4px;
    }
    .back-green-td{
        text-align:left;
        background:#edfec3;
       padding:2px;
    }


</style>

<table border="0" cellpadding="0" cellspacing="0" width="800">
    <tbody>
        <tr>
            <td style="line-height:180%">
                <p>
                    親愛的 {{ $signup->user->profile->fullname  }} 學員：
                </p>    
                <p>
                    
               您已錄取本課程，請於4日內繳交課程費用，方完成報名手續；若未在繳費期限內完成繳費，則視同放棄報名資格，名額將由備取人員自動遞補。
                </p>
               
            </td>
        </tr>
        <tr>
            <td style="line-height:180%">
              
                <p>
                    以下為您於<span class="highlight">{{ $signup->date }}</span>報名的資訊，報名編號為<span class="highlight">{{ $signup->id }}</span>
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <table style="width:99%;border:1px solid #90c32c">
                    <tbody>
                        <tr>
                            <th style="width:120px" class="back-green-th">
                                報名課程
                            </th>
                            <td class="back-green-td"> 
                                {{ $signup->course->name }} ( {{ $signup->course->number }} )
                            </td>
                        </tr>

                        <tr>
                            <th scope="row" class="back-green-th">
                                課程日期
                            </th>
                            <td class="back-green-td"> 
                              {{ $signup->course->begin_date }}  ~  {{ $signup->course->end_date }}
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row" class="back-green-th">
                                上課地點
                            </th>
                            <td class="back-green-td"> 
                                {{ $signup->course->center->addressText }} ( {{ $settings['name'] }}  {{ $signup->course->center->name }} )
                            </td>
                        </tr>
                        <tr>
                            <th style="width:120px" class="back-green-th">
                                姓名
                            </th>
                            <td class="back-green-td"> 
                                 {{ $signup->user->profile->fullname }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="back-green-th">
                                電話
                            </th>
                            <td class="back-green-td"> 
                              {{ $signup->user->phone }}
                            </td>
                        </tr>
                        @if ($signup->discount)
                            <tr>
                                <th scope="row" class="back-green-th">
                                    折扣優惠
                                </th>
                                <td class="back-green-td"> 
                                   
                                    {{ $signup->discount }} / {{ $signup->points }} 折
                                 
                                  
                                </td>
                            </tr>
                         @endif
                         <tr>
                            <th scope="row" class="back-green-th">
                                課程費用
                            </th>
                            <td class="back-green-td"> 
                              {{ $signup->tuition }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="line-height:180%">
                
                <p style="padding-top:14px;">
                    繳費單：請按此開啟繳費單
                </p>
            </td>
        </tr>
        <tr>
            <td style="line-height:180%">
                <p style="padding-top:14px;">
                    繳費方式 :
                    <ul>
                        <li>紙本帳單繳費：請列印繳費單後至台灣銀行、郵局、便利商店(7-11、OK、全家、萊爾富)臨櫃繳費。</li>
                        <li>ATM繳費：詳記您個人繳款帳號後，至ATM轉帳。</li>
                        <li>ibon繳費：取得ibon代碼至7-11列印帳單繳費。</li>
                        <li>手機條碼繳費：透過智慧型手機開啟帳單條碼，至便利商店櫃台繳費。</li>
                    </ul>
                </p>
                
            </td>
        </tr>
       <tr>
            <td style="line-height:180%">
                <p style="padding-top:14px;">
                    依選擇繳費方式之不同，學員需自付手續費8~15元不等。完成繳費後系統將直接匯入您的繳費狀況，系統入帳時間如下：
                    <ul>
                        <li>自動櫃員機（ATM）轉帳：需1～3天</li>
                        <li>台灣銀行臨櫃繳款：需1～3天</li>
                        <li>郵局臨櫃繳款：需4～6天</li>
                        <li>便利商店繳款：需3～5天</li>
                    </ul>
                </p>
                
            </td>
        </tr>
        <tr>
            <td style="line-height:180%">
               
                <p>
                    若有任何問題，請撥以下的服務專線與我們聯絡！</p>
            </td>
        </tr>
        <tr>
            <td style="line-height:180%">
                <p>

                   {{ $settings['name'] }} {{ $signup->course->center->name }} {{ $signup->course->center->phone }}
                </p>
                
            </td>
        </tr>
    </tbody>
</table>

@endcomponent
