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
                        
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="line-height:180%">
                
                <p style="padding-top:14px;">
                    ※本課程的報名截止日為
                    <span class="highlight"> {{ $signup->course->close_date }} </span> 。
                   
                   錄取名單將於報名截止後10天公佈（實際公佈時間以網站公告為主），
                   請上{{ $settings['name'] }}網站 查詢錄取名單，若遇假日則延後公佈。
                   系統將另以E-mail及簡訊通知是否錄取，
                   錄取者將同時收到繳費通知信。
                </p>
            </td>
        </tr>
        <tr>
            <td style="line-height:180%">
                <p style="padding-top:14px;">
                請確認您的報名資料 :
                </p>
                
            </td>
        </tr>
        <tr>
            <td>
                 <table style="width:99%;border:1px solid #90c32c">
                    <tbody>
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
                    </tbody>
                </table>
        </tr>
        <tr>
            <td style="line-height:180%">
                <p>

                    ※若欲修改個人資料，請登入{{ $settings['name'] }}網站  {{ $settings['url'] }}
                </p>
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
