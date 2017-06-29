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
<h2>退費申請</h2>
<table >
    <tr> 
        <th style="width:15%">學員姓名</th> 
        <td style="width:18%;padding-left: 5px;">
           {{ $signup->user->profile->fullname  }} 
        </td> 
        <th style="width:15%">申請日期</th> 
        <td style="width:18%;padding-left: 5px;">
           {{ $signup->refund->date  }}  
        </td> 
        <th style="width:15%">單號</th> 
        <td style="width:18%;padding-left: 5px;">
           {{ $signup->refund->number  }} 
        </td> 
        
    </tr>
    <tr> 
        <th style="width:15%" >課程名稱</th> 
        <td style="width:18%;padding-left: 5px;">
            {{ $signup->course->number  }} &nbsp;
            {{ $signup->course->name  }}
        </td>
        <th style="width:15%" >已上課時數</th> 
        <td style="width:18%;padding-left: 5px;">
           {{ $signup->refund->courses_done  }}
        </td>
        <th style="width:15%" >課程總時數</th> 
        <td style="width:18%;padding-left: 5px;">
             {{ $signup->course->hours  }}
        </td>
    </tr> 
    <tr> 
        <th style="width:15%">退費比例</th> 
        <td style="width:18%;padding-left: 5px;">
             {{ $signup->refund->points  }} &nbsp; %
        </td>
        <th style="width:15%">可退學費</th> 
        <td style="width:18%;padding-left: 5px;">
             {{ $signup->refund->tuition  }} 
        </td>
         <th style="width:15%">手續費</th> 
        <td style="width:18%;padding-left: 5px;">
             {{ $signup->refund->charge  }}
        </td>
    </tr> 
    <tr> 
        <th style="width:15%">應退總金額</th> 
        <td style="width:18%;padding-left: 5px;">
             {{ $signup->refund->total  }}
        </td>
        <th style="width:15%">匯款銀行帳號</th> 
        <td colspan="3" style="padding-left: 5px;">
             {{ $signup->refund->bank_branch  }} &nbsp;
             {{ $signup->refund->account_number  }} &nbsp;
             {{ $signup->refund->account_owner  }} &nbsp;
        </td>
       
    </tr> 
</table>



<br>
<table>
    <tr> 
        <th style="width:15%">退費標準</th>
        <td style="padding-left: 5px;"> 
          
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
               <li>
                  一律以匯款方式退費，不提供現金退費。
              </li>
          </ul>
         
        </td> 
    </tr> 
</table>
<br>
<table style="table-layout:fixed">
    <tr>
        <th>校長</th> 
        <th>主秘</th> 
        <th>主任</th> 
        <th>會計</th> 
        <th>經辦</th> 
    </tr>
     <tr >
        <td style="height: 60px;"></td> 
        <td></td> 
        <td></td> 
        <td></td> 
        <td></td> 
    </tr>
</table>

</body>
</html>