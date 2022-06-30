<?
include('subpage/dbcon.php'); 
include('subpage/check.php');
if (is_login()){}
else{
    header("Location: welcome.php");
}

date_default_timezone_set('Asia/Seoul');
//오늘 날짜
$nowyear = date('Y');
$nowmonth = date('n'); 
$today = date('j'); 
$nowday = date('Y-m-d'); 

//users DB 조회 
try { 
    $session_userid=$_SESSION['user_id'];
    $stmt = $con->prepare('select * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
} 
catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$idrow = $stmt->fetch();  



//연차 년수에 따른 지급일 배열
$vac_day_form_word_years = array('','15','15','16','16','17','17','17','17','17','19','20','20','20','20','20','20','20','20','20','24','25','25','25','25','25','25');

//근무 시작일 -> 년 월 일 분할 
$start_year = date("Y",strtotime($idrow['startday']));
$start_month = date("m",strtotime($idrow['startday']));
$start_day =date("d",strtotime($idrow['startday']));

//지금-입사일 차이 연산
$diff = abs(strtotime($nowday) - strtotime($idrow['startday']));
$diffyears = floor($diff / (365*60*60*24));
$diffmonth = floor(($diff - $diffyears * 365*60*60*24) / (30*60*60*24));
$diffdays = floor(($diff - $diffyears * 365*60*60*24 - $diffmonth*30*60*60*24)/ (60*60*24));

$array_vac_case = array("","연차","반차","반반차","대체휴가","반대체휴가","반반대체휴가","공가","생일휴가","기타");

$count_used_case =  array('0','0','0','0','0','0','0','0','0','0');


//사용 시작일, 유효기간, 연산
if ($diffyears>=2) { 
    $start_date_of_used = $start_year + $diffyears.'-'.$start_month.'-'.$start_day;//사용 시작일

    $validity_year = $start_year + $diffyears + 1; //유효기간
    $validity_month = $start_month;
    $validity_day = $start_day - 1;
    if($validity_day <= 0){
        $validity_month = $validity_month - 1;
        if($validity_month <=0){ $validity_month = 12; $validity_year--; }
        $validity_day = date('t', mktime(0, 0, 0, $validity_month, 1, $validity_year));
    }
    $validity = $validity_year.'년 '.$validity_month.'월 '.$validity_day.'일';  //유효기간

    $next_payment_date_year = $start_year + $diffyears + 1;
    $next_payment_date = $next_payment_date_year.'년 '.$start_month.'월 '.$start_day.'일'; //다음 지급 날자
    $next_payment_day = $vac_day_form_word_years[$diffyears+1]; //다음 지급 일수
}
else if ($diffyears>=1) { 
    $start_date_of_used = $start_year.'-'.$start_month.'-'.$start_day;  //사용 시작일
    
    $validity_year = $start_year + $diffyears + 1; //유효기간
    $validity_month = $start_month;
    $validity_day = $start_day - 1;
    if($validity_day <= 0){
        $validity_month = $validity_month - 1;
        if($validity_month <=0){ $validity_month = 12; $validity_year--; }
        $validity_day = date('t', mktime(0, 0, 0, $validity_month, 1, $validity_year));
    }
    $validity = $validity_year.'년 '.$validity_month.'월 '.$validity_day.'일';  //유효기간

    $next_payment_date_year = $start_year + 2;
    $next_payment_date = $next_payment_date_year.'년 '.$start_month.'월 '.$start_day.'일';  //다음 지급 날자
    $next_payment_day = $vac_day_form_word_years[$diffyears+1];//다음 지급 일수
}
else if ($diffyears>=0) { 
    $con_start_date_of_used_month = $start_month; //사용 시작일
    $con_start_date_of_used_year = $start_year;
    if($con_start_date_of_used_month>=13){
        $con_start_date_of_used_year = $start_year + 1;
        $con_start_date_of_used_month = 1;
    }
    $start_date_of_used = $con_start_date_of_used_year.'-'.$con_start_date_of_used_month.'-'.$start_day;  //사용 시작일
    
    $validity_year = $start_year + 2; //유효기간
    $validity_day = $start_day - 1;
    $validity_month = $start_month;
    if($validity_day <= 0){
        $validity_month = $validity_month - 1;
        if($validity_month <=0){ $validity_month = 12; $validity_year--; }
        $validity_day = date('t', mktime(0, 0, 0, $validity_month, 1, $validity_year));
    }
    $validity = $validity_year.'년 '.$validity_month.'월 '.$validity_day.'일';  //유효기간

    $con_next_payment_date_month = $start_month + $diffmonth + 1;
    $con_next_payment_date_year = $start_year;
    if ($con_next_payment_date_month>=13) {
        $con_next_payment_date_month = 1;
        $con_next_payment_date_year++;
    }
    $next_payment_date = $con_next_payment_date_year.'년 '.$con_next_payment_date_month.'월 '.$start_day.'일';  //다음 지급 날자
    $next_payment_day = 1;//다음 지급 일수
}

//출력할 날짜 구하기
$nowday = date('Y-m-d'); //오늘날짜

$output_start_day = $start_date_of_used; //DB 조건용 출력 시작날짜
$output_end_day = $validity; //DB 조건용 출력 종료날짜

$usedaycount=0; //사용 개수 체크용 변수

//vacation DB 조회
try { 
    $stmt = $con->prepare('SELECT * FROM vacation WHERE activate = 1 and uid=:uid and va_day_start>=:output_start_day and va_day_start<=:output_end_day ORDER BY va_day_start asc');
    $stmt->bindParam(':uid',$idrow['uid']);
    $stmt->bindParam(':output_start_day',$start_date_of_used);
    $stmt->bindParam(':output_end_day',$output_end_day);
    $stmt->execute();
} 
catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}


if ($stmt->rowCount() > 0){
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $holiday_index = 0;
        $year = date('Y', strtotime($row['va_day_start']));
        $holiday_array = array('',$year.'-01-01',$year.'-02-04',$year.'-02-05',$year.'-02-06',$year.'-03-01',$year.'-05-05',$year.'-05-12',$year.'-06-06',$year.'-08-15',$year.'-09-12',$year.'-09-13',$year.'-09-14',$year.'-10-03',$year.'-10-09',$year.'-12-25');
        $holiday_index = array_search($row['va_day_start'], $holiday_array);
        $day_of_the_week = 0;
        $day_of_the_week_array = array("1","0","0","0","0","0","1");
        $day_of_the_week = ($day_of_the_week_array[date('w', strtotime($row['va_day_start']))]);
        if($holiday_index == 0 and $day_of_the_week == 0 and $row['activate']== 1 ){
            switch ($row['va_case']) {
                case '1': $usedaycount++; break;
                case '2': $usedaycount = $usedaycount + 0.5; break;
                case '3': $usedaycount = $usedaycount + 0.25; break;
                default: break;
            }
            $count_used_case[$row['va_case']]++;
        }
    }
}
if ($diffyears>=2) { 
    $haveday = $vac_day_form_word_years[$diffyears] - $usedaycount; //보유한 휴가 개수
    $paidday = $vac_day_form_word_years[$diffyears];
}
else if ($diffyears>=1) { 
    if($diffmonth==0){
        $haveday = 11 - $usedaycount; //보유한 휴가 개수
        $paidday = 11;
    }
    else{
        $haveday = $vac_day_form_word_years[$diffyears] + 11 - $usedaycount; //보유한 휴가 개수
        $paidday = $vac_day_form_word_years[$diffyears] + 11;
    }
}
else if ($diffyears>=0) { 
    $haveday = $diffmonth - $usedaycount; //보유한 휴가 개수
    $paidday = $diffmonth;
}
?>  

<!DOCTYPE html>
<html>
<head>
    <title>휴가 - 휴가테이블 </title>
    <!--공동 선언 헤더-->
    <? include('subpage/head.html') ?>
    <!--개별 선언 헤더-->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/apply.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="http://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <? include('subpage/navigation.php'); ?>
        <div class="container">
            <? include('subpage/header.php'); ?>
            <div class="content">
                <div class="check_wrap">
                    <div class="check_form_title">
                        <h1> 휴가 사용 내역</h1>
                        <button class="button_effect_0 on_apply_popup f-right">휴가 신청하기</button>
                        <div class="guide_word">
                            <div class="">
                            </div>
                            <?
                            if($diffyears>=1){
                                echo "조회 기간 : ( ";
                                if($diffyears>=2)

                                    echo date("Y",strtotime($start_date_of_used))."년 ". date("n",strtotime($start_date_of_used))."월 ". date("j",strtotime($start_date_of_used))."일";
                                
                                else
                                    echo "입사일";
                                
                                echo " )  ~  ( ".$validity." )" ; 
                                echo "<div> 남아있는 휴가는 ".$validity."에 소멸 예정입니다.</div>";
                            }
                            else{
                                echo "입사일부터의 데이터를 조회중입니다...";
                            }?>
                        </div>
                </div>
                <div class="check_form">
                    <div class="main_check">
                        <div class="top_group">
                            <div class="group_form w-300 pr-4 mr-5 border-top-left-5 border-top-right-5">연차 </div>
                            <div class="group_form w-300 pr-4 mr-5 mr-5 border-top-left-5 border-top-right-5">대체휴가</div>
                            <div class="group_form w-300 pr-4 mr-5 mr-5 border-top-left-5 border-top-right-5">기타</div>
                        </div>
                        <div class="middle_group">
                            <div class="group_form w-100">연차</div>
                            <div class="group_form w-100">반차</div>
                            <div class="group_form w-100 mr-5">반반차</div>
                            <div class="group_form w-100">대체휴가</div>
                            <div class="group_form w-100">반휴가</div>
                            <div class="group_form w-100 mr-5">반반휴가</div>
                            <div class="group_form w-100">공가</div>
                            <div class="group_form w-100">생일휴가</div>
                            <div class="group_form w-100">기타</div>
                        </div>
                        <div class="bottom_group">
                            <div class="group_form w-100 border-bottom-left-5"><? echo $count_used_case[1]?></div>
                            <div class="group_form w-100"><? echo $count_used_case[2]?></div>
                            <div class="group_form w-100 mr-5 border-bottom-right-5"><? echo $count_used_case[3]?></div>
                            <div class="group_form w-100 border-bottom-left-5"><? echo $count_used_case[4]?></div>
                            <div class="group_form w-100"><? echo $count_used_case[5]?></div>
                            <div class="group_form w-100 mr-5 border-bottom-right-5"><? echo $count_used_case[6]?></div>
                            <div class="group_form w-100 border-bottom-left-5"><? echo $count_used_case[7]?></div>
                            <div class="group_form w-100"><? echo $count_used_case[8]?></div>
                            <div class="group_form w-100 border-bottom-right-5"><? echo $count_used_case[9]?></div>
                        </div>
                    </div>
                    <div class="sub_check">
                        <div class="top_group">
                            <div class="group_form w-200 pr-4 border-top-left-5 border-top-right-5">입사일</div>
                            <div class="group_form w-100 mr-5 border-top-left-5 border-top-right-5">근속 연수</div>
                            <div class="group_form w-100 border-top-left-5 border-top-right-5">지급 개수</div>
                            <div class="group_form w-100 border-top-left-5 border-top-right-5">사용한 연차</div>
                            <div class="group_form w-100 mr-5 border-top-left-5 border-top-right-5">사용 가능 연차</div>
                            <div class="group_form w-200 pr-4 border-top-left-5 border-top-right-5">다음 휴가 지급일</div>
                            <div class="group_form w-100 border-top-left-5 border-top-right-5">다음 지급 개수</div>
                        </div>
                        <div class="bottom_group">
                            <div class="group_form w-200 pr-4 border-bottom-left-5 border-bottom-right-5"><? echo date("Y",strtotime($idrow['startday']))?>년 <? echo date("n",strtotime($idrow['startday']))?>월 <? echo date("j",strtotime($idrow['startday']))?>일</div>
                            <div class="group_form mr-5 w-100 border-bottom-left-5 border-bottom-right-5"><? echo $diffyears?>년 <? echo $diffmonth?>개월</div>
                            <div class="group_form w-100 border-bottom-left-5 border-bottom-right-5"><? echo $paidday;?>일</div>
                            <div class="group_form w-100 border-bottom-left-5 border-bottom-right-5"><? echo $usedaycount;?>일</div>
                            <div class="group_form mr-5 w-100 border-bottom-left-5 border-bottom-right-5"><? echo $haveday ?>일</div>
                            <div class="group_form w-200 pr-4 border-bottom-left-5 border-bottom-right-5"><? echo $next_payment_date?></div>
                            <div class="group_form w-100 border-bottom-left-5 border-bottom-right-5"><? echo $next_payment_day;?>일</div>
                        </div>
                    </div>
                </div>
            </div>
            <? include ('subpage/user_usage_inquiry.php')?>
            <? include ('subpage/apply_form.php')?>
        </div>
    </div>
</div>
</body>
</html>

