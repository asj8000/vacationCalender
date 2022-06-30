<?php 
try { 
    $session_userid=$_SESSION['user_id'];
    $stmt = $con->prepare('select * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
    
} catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$idrow = $stmt->fetch();  
$userauthority=$idrow['authority'];


$width_control_array = array('','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50');
$width_array_con = 0; 
for ($i=0; $i < $diff_months_all; $i++) { $width_array_con++; } //하단 출력 폼 width 값 조정용


$array_team =  array("none","마케팅","VOD","totur","VI","CX","B2B","other");
if ($idrow['authority']==2) {
    $stmt = $con->prepare('SELECT * FROM users ORDER BY startday asc');
    echo "전체 팀원의 휴가 사용 횟수를 조회합니다";
}
else if ($idrow['authority']==1) {
    $stmt = $con->prepare('SELECT * FROM users WHERE team=:team ORDER BY startday asc');
    $stmt->bindParam(':team',$idrow['team']);
    echo $array_team[$idrow['team']]."팀원의 휴가 사용 횟수를 조회합니다.";
}
$stmt->execute();
?>

<div class="top_group w-1100">
    <div class="group_form w-100">No</div>
    <div class="group_form w-100">부서</div>
    <div class="group_form w-100">사원명</div>
    <div class="group_form w-200">닉네임</div>
    <div class="group_form w-200">입사일</div>
    <div class="group_form w-100">근속년수</div>
    <div class="group_form w-100">지급된 연차</div>
    <div class="group_form w-100">사용한 연차</div>
    <div class="group_form w-100">남은 연차</div>
</div>
<talbe class="bottom_group">
    <?php
    $No_count_index=0;
    if ($stmt->rowCount() > 0){
        while($idrow=$stmt->fetch(PDO::FETCH_ASSOC)){
            if($idrow['activate']==1){
                echo '<div class="group_wrap w-1100">';
                $No_count_index++;
                echo '<div class="group_form w-100">'.$No_count_index.'</div>';
                echo '<div class="group_form w-100">'.$array_team[$idrow['team']].'</div>';
                echo '<div class="group_form w-100">'.$idrow['username'].'</div>';
                echo '<div class="group_form w-200 center">'.$idrow['userprofile'].'</div>';
                echo '<div class="group_form w-200">'.$idrow['startday'].'</div>';

                //근무 시작일 -> 년 월 일 분할 
                $start_year = date("Y",strtotime($idrow['startday']));
                $start_month = date("m",strtotime($idrow['startday']));
                $start_day =date("d",strtotime($idrow['startday']));
                //지금-입사일 차이 연산
                $diff = abs(strtotime($nowday) - strtotime($idrow['startday']));
                $diffyears = floor($diff / (365*60*60*24));
                $diffmonth = floor(($diff - $diffyears * 365*60*60*24) / (30*60*60*24));
                $diffdays = floor(($diff - $diffyears * 365*60*60*24 - $diffmonth*30*60*60*24)/ (60*60*24));
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

                }

                //출력할 날짜 구하기
                $nowday = date('Y-m-d'); //오늘날짜

                $output_start_day = $start_date_of_used; //DB 조건용 출력 시작날짜
                $output_end_day = $validity; //DB 조건용 출력 종료날짜
                try { //여부분 만들자
                    $vacstmt = $con->prepare('SELECT * FROM vacation WHERE uid=:uid and va_day_start>=:output_start_day and va_day_start<=:output_end_day ORDER BY va_day_start asc');
                    $vacstmt->bindParam(':output_start_day',$output_start_day);
                    $vacstmt->bindParam(':output_end_day',$output_end_day_plus);
                    $vacstmt->bindParam(':uid',$idrow['uid']);
                    $vacstmt->execute();
                } 
                catch(PDOException $e) {
                    die("Database error. " . $e->getMessage()); 
                }            $haveday = 0;
                $usage_date_all = 0;
                $usage_count_index = array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0');
                if ($vacstmt->rowCount() > 0){
                    while($vacrow=$vacstmt->fetch(PDO::FETCH_ASSOC)){

                        extract($vacrow);
                        $holiday_index = 0;
                        $holiday_index = array_search($vacrow['va_day_start'], $holiday_array);
                        $day_of_the_week = 0;
                        $day_of_the_week_array = array("1","0","0","0","0","0","1");
                        $day_of_the_week = ($day_of_the_week_array[date('w', strtotime($vacrow['va_day_start']))]);

                        if($holiday_index == 0 and $day_of_the_week == 0 and $vacrow['activate']== 1 ){
                            if($vacrow['va_case']==1){ $usage_date_all++;}
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
                }/*
                for ($i=0; $i < $diff_months_all; $i++) { //출력부분 ?> 
                    <div class="group_form w-100">
                        <div class="">
                            <?php 
                            if ($usage_count_index[$i]!=0){
                            echo '<div class="usage_count_point">'.$usage_count_index[$i].'</div>' ;
                            }
                            else{
                                echo "-";
                            }?>
                        </div>
                    </div>
                <?php }*/
                if ($diffyears != 0) {
                echo '<div class="group_form w-100">'.$diffyears.'년'.$diffmonth.'개월</div>';
                }
                else {
                echo '<div class="group_form w-100">'.$diffmonth.'개월</div>';
                }
                echo '<div class="group_form w-100">'.$paidday.'</div>';
                echo '<div class="group_form w-100">'.$usage_date_all.'</div>';
                echo '<div class="group_form w-100">'.$haveday.'</div>';
                echo '</div>';
            }
        }
    }
    ?>  
</table>