<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title> {{ $title }}</title>

        <style>
        body { font-family:  'MSJH'; } 
        table{
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


        </style>
    </head>



<body> 
<h2>慈大社推 &nbsp; 台北中心 &nbsp; 課程費用收據</h2>
<table style="width:99%;">
    <tr> 
        <th style="width:15%">學員姓名</th> 
        <td style="width:35%;padding-left: 5px;">
           {{ $signup->user->profile->name  }} 
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
           {{  $signup->tuition }}
        </td>
        <th style="width:15%" >已繳費用</th> 
        <td style="width:35%;padding-left: 5px;">
            {{ $invoice->money }} 
        </td>
    </tr> 
    <tr> 
        <th style="width:15%">繳費日期</th> 
        <td style="width:35%;padding-left: 5px;"> 
             {{ $invoice->date }} 
        </td> 
        <th style="width:15%">繳費方式</th> 

        <td style="width:35%;padding-left: 5px;">
            @foreach ($lesson->volunteers as $volunteer)
                {{ $volunteer->name  }} &nbsp;
            @endforeach
        </td>
    </tr> 
</table>


</body>
</html>