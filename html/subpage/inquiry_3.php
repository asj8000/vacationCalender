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
?>
<div class="inquiry_date_con">
    <form method="GET" id="form" > 
        <input type="month" name="first_day" value="<?php echo $output_start_day?>"> ~ <input type="month" name="last_day" value="<?php echo $output_end_day?>">
        <button type="submit" name="submit" class="button_effect_1" >조회</button>
    </form>
</div>
<?
echo "위 기간동안 ";
if ($idrow['authority']==2) {
	$stmt = $con->prepare('SELECT * FROM users ORDER BY startday asc');
	echo "전체 팀원의";
}
else if ($idrow['authority']==1) {
	$stmt = $con->prepare('SELECT * FROM users WHERE team=:team ORDER BY startday asc');
	$stmt->bindParam(':team',$idrow['team']);
	$array_team =  array("none","마케팅","VOD","totur","VI","CX","B2B","other");
	echo $array_team[$idrow['team']]."팀원의";
}
echo " 휴가 사용 횟수를 각 유형별로 조회합니다.";
$stmt->execute();
$output_end_day_plus = $output_end_day;
$output_end_day_plus++;
?>
<div class="top_group w-1500">
    <div class="title group_form w-200 br-1-solid-ccc"></div>
    <div class="title group_form w-300 br-1-solid-ccc">연차</div>
    <div class="title group_form w-300 br-1-solid-ccc">대체휴가</div>
    <div class="title group_form w-300 br-1-solid-ccc">기타</div>
    <div class="title group_form w-300 br-1-solid-ccc">통계</div>

</div>
<div class="top_group w-1500">
    <div class="group_form w-200 br-1-solid-ccc"></div>
    <div class="group_form w-100">연차</div>
    <div class="group_form w-100">반차</div>
    <div class="group_form w-100 br-1-solid-ccc">반반차</div>
    <div class="group_form w-100">대체휴가</div>
    <div class="group_form w-100">반휴가</div>
    <div class="group_form w-100 br-1-solid-ccc">반반휴가</div>
    <div class="group_form w-100">공가</div>
    <div class="group_form w-100">생일휴가</div>
    <div class="group_form w-100 br-1-solid-ccc">기타</div>
    <div class="group_form w-100">총 휴가 횟수</div>
    <div class="group_form w-100">소진한 연차</div>
    <div class="group_form w-100 br-1-solid-ccc">사용 가능 연차</div>
</div>
<div class="bottom_group">
    <?php
    if ($stmt->rowCount() > 0){
        while($idrow=$stmt->fetch(PDO::FETCH_ASSOC)){
            if($idrow['activate']==1){
                $diff = 0;//오류방지용
                echo '<div class="group_wrap w-1400">';
                echo '<div class="group_form w-200 center">'.$idrow['userprofile'].'</div>';
                try { //여부분 만들자
                    $vacstmt = $con->prepare('SELECT * FROM vacation WHERE uid=:uid and va_day_start>=:output_start_day and va_day_start<=:output_end_day ORDER BY va_day_start asc');
                    $vacstmt->bindParam(':output_start_day',$output_start_day);
    				$vacstmt->bindParam(':output_end_day',$output_end_day_plus);
                    $vacstmt->bindParam(':uid',$idrow['uid']);
                    $vacstmt->execute();
                } 
                catch(PDOException $e) {
                    die("Database error. " . $e->getMessage()); 
                }
                $usage_count_all = 0;
                $usage_date_all = 0;
                $haveday = 0;
                $usage_count_index = array('0','0','0','0','0','0','0','0','0','0','0','0','0');
                $usage_count_detail = array('','','','','','','','','','','','','','');
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
                            switch ($vacrow['va_case']) {
                                case '1': $usage_count_index[1]++; $usage_count_detail[1] = $usage_count_detail[1].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                case '2': $usage_count_index[2]++; $usage_count_detail[2] = $usage_count_detail[2].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                case '3': $usage_count_index[3]++; $usage_count_detail[3] = $usage_count_detail[3].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                case '4': $usage_count_index[4]++; $usage_count_detail[4] = $usage_count_detail[4].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                case '5': $usage_count_index[5]++; $usage_count_detail[5] = $usage_count_detail[5].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                case '6': $usage_count_index[6]++; $usage_count_detail[6] = $usage_count_detail[6].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                case '7': $usage_count_index[7]++; $usage_count_detail[7] = $usage_count_detail[7].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                case '8': $usage_count_index[8]++; $usage_count_detail[8] = $usage_count_detail[8].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                case '9': $usage_count_index[9]++; $usage_count_detail[9] = $usage_count_detail[9].$vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];break;
                                default:
                                    # code...
                                    break;

                            }
                            ++$usage_count_all;
                        }
                    }   
                }
                //지금-입사일 차이 연산
                $nowday = date('Y-m-d'); 
                $diff = $nowday;//오류방지용
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
                else if ($diffyears<=0) { 
                    $haveday = $diffmonth - $usage_date_all; //보유한 휴가 개수
                    $paidday = $diffmonth;
                }
                for ($i=1; $i <= 9; $i++) { //출력부분 ?> 
                    <div class="group_form w-100">
                        <div class="">
                            <?php 
                            if ($usage_count_index[$i]!=0){
                            echo '<div class="usage_count_point"><span>'.$usage_count_index[$i].'</span><p class="arrow_box">'.$usage_count_detail[$i].'</p></div>' ;
                            }
                            else{
                                echo "-";
                            }?>
                        </div>
                    </div>
                <?php }
                echo '<div class="group_form w-100">'.$usage_count_all.'</div>';
                echo '<div class="group_form w-100">'.$usage_date_all.'</div>';
                echo '<div class="group_form w-100">'.$haveday.'</div>';
                echo '</div>';
            }
        }
    }
    ?>  
</div>