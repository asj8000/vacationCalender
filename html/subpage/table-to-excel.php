<?php
include ('Classes/PHPExcel.php');
 include('../subpage/dbcon.php'); 
include('../subpage/check.php');


try { 
    $session_userid=$_SESSION['user_id'];
    $stmt = $con->prepare('select * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
    
} catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$idrow = $stmt->fetch();  
$No_count_index = 0;
$nowday = date("Y-m-d H:i:s");

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = excel_test.xls" );     //filename = 저장되는 파일명을 설정합니다.
header( "Content-Description: PHP4 Generated Data" );


// HTML 테이블
$EXCEL_FILE = "<meta http-equiv='content-type' content='text/html; charset=utf-8'>
이 문서는".$nowday."에 ".$idrow['userprofile']."님에 의해 제작되었습니다.
<table>
	<tr>
       <td>No</td>
       <td>부서</td>
       <td>사원명</td>
       <td>닉네임</td>
       <td>입사일</td>
       <td>근속년수</td>
       <td>지급된연차</td>
       <td>사용한연차</td>
       <td>남은 연차</td>
    </tr>";

//엑셀 파일로 만들고자 하는 데이터의 테이블을 만듭니다.



if ($idrow['authority']==2) {
    $stmt = $con->prepare('SELECT * FROM users ORDER BY startday asc');
}
$stmt->execute();
if ($stmt->rowCount() > 0){
    while($idrow=$stmt->fetch(PDO::FETCH_ASSOC)){
        if($idrow['activate']==1){
            try {
                $vacstmt = $con->prepare('SELECT * FROM vacation WHERE uid=:uid ORDER BY va_day_start asc');
                $vacstmt->bindParam(':uid',$idrow['uid']);
            } 
            catch(PDOException $e) {
                die("Database error. " . $e->getMessage()); 
            }              
            $vacstmt->execute();
            $haveday = 0;
            $usage_date_all = 0;
            $usage_count_all = 0;
            $usage_count_index = array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0');
            $usage_count_detail = array('','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
            $nowday = date('Y-m-d'); 
            //근무 시작일 -> 년 월 일 분할 
            $start_year = date("Y",strtotime($idrow['startday']));
            $start_month = date("m",strtotime($idrow['startday']));
            $start_day =date("d",strtotime($idrow['startday']));
            //지금-입사일 차이 연산
            $diff = abs(strtotime($nowday) - strtotime($idrow['startday']));
            $diffyears = floor($diff / (365*60*60*24));
            $diffmonth = floor(($diff - $diffyears * 365*60*60*24) / (30*60*60*24));
            $diffdays = floor(($diff - $diffyears * 365*60*60*24 - $diffmonth*30*60*60*24)/ (60*60*24));
            if ($diffyears>=2) { 
                $start_date_of_used = $start_year + $diffyears.'-'.$start_month.'-'.$start_day;//사용 시작일
            }
            else if ($diffyears>=1) { 
                $start_date_of_used = $start_year.'-'.$start_month.'-'.$start_day;  //사용 시작일
            }
            else if ($diffyears>=0) { 
                $con_start_date_of_used_month = $start_month; //사용 시작일
                $con_start_date_of_used_year = $start_year;
                if($con_start_date_of_used_month>=13){
                    $con_start_date_of_used_year = $start_year + 1;
                    $con_start_date_of_used_month = 1;
                }
                $start_date_of_used = $con_start_date_of_used_year.'-'.$con_start_date_of_used_month.'-'.$start_day;  //사용 시작일
            }
            if ($vacstmt->rowCount() > 0){
                while($vacrow=$vacstmt->fetch(PDO::FETCH_ASSOC)){
                    extract($vacrow);
                    $year = date('Y', strtotime($vacrow['va_day_start']));
                    $holiday_array = array('',$year.'-01-01',$year.'-02-04',$year.'-02-05',$year.'-02-06',$year.'-03-01',$year.'-05-05',$year.'-05-12',$year.'-06-06',$year.'-08-15',$year.'-09-12',$year.'-09-13',$year.'-09-14',$year.'-10-03',$year.'-10-09',$year.'-12-25');
                    $holiday_index = array_search($vacrow['va_day_start'], $holiday_array);
                    $day_of_the_week = 0;
                    $day_of_the_week_array = array("1","0","0","0","0","0","1");
                    $day_of_the_week = ($day_of_the_week_array[date('w', strtotime($vacrow['va_day_start']))]);

                    if($holiday_index == 0 and $day_of_the_week == 0 and $vacrow['activate']==1 and $vacrow['va_day_start'] >= $start_date_of_used){
                        if($vacrow['va_case']==1){ $usage_date_all = $usage_date_all+1;}
                        else if($vacrow['va_case']==2){ $usage_date_all=$usage_date_all+0.5;}
                        else if($vacrow['va_case']==3){ $usage_date_all=$usage_date_all+0.25;}

                    }
                }
            }
    		//지금-입사일 차이 연산

            $nowday = date('Y-m-d'); 
            $diff = abs(strtotime($nowday) - strtotime($idrow['startday']));
            $diffyears = floor($diff / (365*60*60*24));
            $diffmonth = floor(($diff - $diffyears * 365*60*60*24) / (30*60*60*24));
            $vac_day_form_word_years = array('','15','15','16','16','17','17','17','17','17','19','20','20','20','20','20','20','20','20','20','24','25','25','25','25','25','25');
            if ($diffyears>=2) { 
                $haveday = $vac_day_form_word_years[$diffyears] - $usage_date_all; //보유한 휴가 개수
                $paidday = $vac_day_form_word_years[$diffyears];
            }
            else if ($diffyears>=1) { 
                if($diffmonth==0){
                    $haveday = 11 - $usage_date_all; //보유한 휴가 개수
                    $paidday = 11;
                }
                else{
                    $haveday = $vac_day_form_word_years[$diffyears] + 11 - $usage_date_all; //보유한 휴가 개수
                    $paidday = $vac_day_form_word_years[$diffyears] + 11;
                }
            }
            else if ($diffyears>=0) { 
                $haveday = $diffmonth - $usage_date_all; //보유한 휴가 개수
                $paidday = $diffmonth;
            }
            if($diffyears!=0){
            	$difftext = $diffyears.'년'.$diffmonth.'개월';
            }
            else{
            	$difftext =$diffmonth.'개월';
            }

            $No_count_index++;
    		$array_team =  array("none","마케팅","VOD","totur","VI","CX","B2B","other");
            $EXCEL_FILE .= '
                <tr>                
                    <td>'.$No_count_index.'</td>
                    <td>'.$array_team[$idrow['team']].'</td>
                    <td>'.$idrow['username'].'</td>
                    <td>'.$idrow['userprofile'].'</td>
                    <td>'.$idrow['startday'].'</td>
                    <td>'.$difftext.'</td>
                    <td>'.$paidday.'</td>
                    <td>'.$usage_date_all.'</td>
                    <td>'.$haveday.'</td>
                </tr>
            ';
        }
    }
}
$EXCEL_FILE .= '</table>';

$EXCEL_FILE .= "
<table>
	<tr>
       <td>부서</td>
       <td>사원명</td>
       <td>닉네임</td>
       <td>사용 날짜</td>
       <td>사용 유형</td>
       <td>최초등록시간</td>
    </tr>";

try { //여부분 만들자
    $stmt = $con->prepare('SELECT * FROM vacation ORDER BY va_day_start desc');
    $stmt->execute();
} 
catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}

if ($stmt->rowCount() > 0){
    while($vacrow=$stmt->fetch(PDO::FETCH_ASSOC)){
        extract($vacrow);
		$year = date('Y', strtotime($vacrow['va_day_start']));
		$holiday_array = array('',$year.'-1-1',$year.'-2-4',$year.'-2-5',$year.'-2-6',$year.'-3-1',$year.'-5-5',$year.'-5-12',$year.'-6-6',$year.'-8-15',$year.'-9-12',$year.'-9-13',$year.'-9-14',$year.'-10-3',$year.'-10-9',$year.'-12-25');
		$array_vac_case = array("","연차","반차","반반차","대체휴가","반대체휴가","반반대체휴가","공가","생일휴가","기타");
        $holiday_index = 0;
        $holiday_index = array_search($vacrow['va_day_start'], $holiday_array);
        $day_of_the_week = 0;
        $day_of_the_week_array_con = array("1","0","0","0","0","0","1");
        $day_of_the_week_array = array("일","월","화","수","목","금","토");
        $day_of_the_week = ($day_of_the_week_array_con[date('w', strtotime($vacrow['va_day_start']))]);


   		try {
		    $stmt_for_user_team = $con->prepare('SELECT * FROM users WHERE uid=:uid');
		    $stmt_for_user_team->bindParam(':uid',$vacrow['uid']);
		    $stmt_for_user_team->execute();
		} 
		catch(PDOException $e) {
		    die("Database error. " . $e->getMessage()); 
		}
		$idrow = $stmt_for_user_team->fetch();  
		$array_team =  array("none","마케팅","VOD","totur","VI","CX","B2B","other");

        if ($idrow){
            if($holiday_index == 0 and $day_of_the_week == 0 and $vacrow['activate']== 1 ){ 
                $EXCEL_FILE .= '
                    <tr>
                        <td>'.$array_team[$idrow['team']].'</td>
                        <td>'.$idrow['username'].'</td>
                        <td>'.$vacrow['userprofile'].'</td>
                        <td>'.$vacrow['va_day_start'].'</td>
                        <td>'.$array_vac_case[$vacrow["va_case"]].'</td>
                        <td>'.$vacrow['vac_regtime'].'</td>
                    </tr>
                ';
            }
        }
    }
}  

$EXCEL_FILE .= '</table>';



// 임시파일 저장 후 로드
$tmpfile = '/tmp/'.uniqid().'.html';
file_put_contents($tmpfile, $EXCEL_FILE);
$reader = new PHPExcel_Reader_HTML; 
$content = $reader->load($tmpfile); 
unlink( $tmpfile );
// 엑셀 출력    




$writer = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="table-to-excel.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');