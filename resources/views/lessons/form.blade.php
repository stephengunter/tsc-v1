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
<h2>課堂紀錄表</h2>
<table style="width:99%;">
    <tr> 
        <th style="width:15%">課程名稱</th> 
        <td style="width:35%;padding-left: 5px;">
           {{ $lesson->course->number  }} &nbsp;
           {{ $lesson->course->name  }}
        </td> 
        <th style="width:15%" >課堂順序</th> 
        <td style="width:35%;padding-left: 5px;">
            {{ $lesson->order  }}
        </td>
    </tr>
    <tr> 
        <th style="width:15%" >上課時間</th> 
        <td style="width:35%;padding-left: 5px;">
           {{  $lesson->time }}
        </td>
        <th style="width:15%" >上課地點</th> 
        <td style="width:35%;padding-left: 5px;">{{ $lesson->course->center->name }} &nbsp; {{ $lesson->classroom->name }}</td>
    </tr> 
    <tr> 
        <th style="width:15%">授課教師</th> 
        <td style="width:35%;padding-left: 5px;"> 
            @foreach ($lesson->teachers as $teacher)
                {{ $teacher->name  }} &nbsp;
            @endforeach
        </td> 
        <th style="width:15%">教育志工</th> 
        <td style="width:35%;padding-left: 5px;">
            @foreach ($lesson->volunteers as $volunteer)
                {{ $volunteer->name  }} &nbsp;
            @endforeach
        </td>
    </tr> 
</table>
<table style="width:99%;">
    
<caption>學員名單</caption>
    @for ($i = 1; $i <= $rows; $i++)
        <tr> 
            <td style="width:5%;text-align: center;">{{ $studentList[$i*2-2 ]->number }}</td> 
            <td style="width:15%;text-align: center;">{{ $studentList[$i*2-2 ]->user->profile->fullname }}</td> 
            <td style="width:15%"></td> 
            <td style="width:15%"></td>

            @if ($studentList[$i*2-1 ])
                <td style="width:5%;text-align: center;">{{ $studentList[$i*2-1 ]->number }}</td> 
                <td style="width:15%;text-align: center;">{{ $studentList[$i*2-1 ]->user->profile->fullname }}</td> 
               
                <td style="width:15%"></td> 
                <td style="width:15%"></td>
           
            @else
                <th style="width:5%"></th> 
                <th style="width:15%"></th> 
                <td style="width:15%"></td> 
                <td style="width:15%"></td>
            @endif
           
        </tr>
    @endfor

      

   
    
    
</table>


</body>
</html>