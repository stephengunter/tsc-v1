<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title> {{ $title }}</title>

        <style>
        body { font-family:  'MSJH'; } 
        table{
                width:99%;
                border-collapse:collapse;
            }
            th, td {
                border:1px solid #aaa;
                height: 35px;
                
            }
            td {
                
                vertical-align: center;
            }
            th {
                vertical-align: center;
                text-align: center;
            }
            caption{
                 height: 35px;
            }
            ul {
               
            }


        </style>
    </head>



<body> 
<h2>慈大社推 &nbsp;  課程費用收據</h2>
<table >
    <tr> 
        <th style="width:15%">學員姓名</th> 
        <td style="width:35%;padding-left: 5px;">
           {{ $signup->user->profile->fullname  }} 
        </td> 
        <th style="width:15%" >課程名稱</th> 
        <td style="width:35%;padding-left: 5px;">
            {{ $signup->course->number  }} &nbsp;
            {{ $signup->course->name  }}
        </td>
    </tr>
    <tr> 
        <th style="width:15%" >課程費用</th> 
        <td style="width:35%;padding-left: 5px;">
           {{  $signup->tuition }} &nbsp;

           @if($signup->discount)
              ( {{ $signup->discount  }}  

                {{ $signup->points  }}

                  折
              )
           @endif
        </td>
        <th style="width:15%" >已繳費用</th> 
        <td style="width:35%;padding-left: 5px;">
            {{ $invoice['money'] }} 
        </td>
    </tr> 
    <tr> 
        <th style="width:15%">繳費日期</th> 
        <td style="width:35%;padding-left: 5px;"> 
           {{ $invoice['date'] }} 
        </td> 
        <th style="width:15%">繳費方式</th> 

        <td style="width:35%;padding-left: 5px;">
            {{ $invoice['payBy'] }} 
        </td>
    </tr> 
</table>
<br>
<table>
    <tr>
        <th style="width:15%">開課中心</th> 
        <td style="width:35%;padding-left: 5px;"> 
           {{ $signup->course->center->name }} &nbsp;
           {{ $signup->course->center->contactInfo->tel }}
        </td>
        <th style="width:15%">經辦人員</th> 
        <td style="width:35%"> 
          
        </td>
    </tr>
    
</table>

<br>
<table>
    <tr> 
        <th style="width:15%">注意事項</th>
        <td style="padding-left: 5px;"> 
          ※完成報名繳費後，因故退學者，依下列標準退費：
          <ul>
              <li>
                  開課前申請退班者，退還已繳學費九折。
              </li>
              <li>
                  開課後未逾全期三分之一申請退班者，退還已繳學費半數。
              </li>
              <li>
                  在班時間已逾全期三分之一者，將不予退還。
              </li>
          </ul>
          ※本中心一律以匯款方式退費，不提供現金退費。
        </td> 
    </tr> 
</table>


</body>
</html>