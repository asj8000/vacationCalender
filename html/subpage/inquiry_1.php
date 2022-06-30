<? 
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
$diff = abs(strtotime($output_end_day) - strtotime($output_start_day));
$diff_years = floor($diff / (365*60*60*24));
$diff_months = floor(($diff - $diff_years * 365*60*60*24) / (30*60*60*24));
$diff_months_all = ($diff_months + $diff_years*12)+1;
$inquiry_wrap_1_output_month = date("n",strtotime($output_start_day));


$width_control_array = array('','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50');
$width_array_con = 0; 
for ($i=0; $i < $diff_months_all; $i++) { $width_array_con++; } //하단 출력 폼 width 값 조정용


?>
<div class="inquiry_date_con">
    <form method="GET" id="form" > 
        <input type="month" name="first_day" min="2017-01" value="<? echo $output_start_day?>"> ~ <input type="month" name="last_day" value="<? echo $output_end_day?>">
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
echo " 휴가 사용 횟수를 월별로 조회합니다.";
$stmt->execute();
$output_end_day_plus = $output_end_day;
$output_end_day_plus++;
$array_vac_case = array("","연차","반차","반반차","대체휴가","반휴가","반반휴가","공가","생일휴가","기타");
?>

<div class="top_group w-<? echo $width_control_array[$width_array_con]?>00">
    <div class="group_form w-200"></div>
    <? 
    for ($i=0; $i < $diff_months_all; $i++) { ?>
        <div class="group_form w-100">
            <div class=""><? echo $inquiry_wrap_1_output_month++;if($inquiry_wrap_1_output_month>=13){$inquiry_wrap_1_output_month=1;} ?>월 </div>
        </div>
    <? }?>
    <div class="group_form w-100">총 휴가 횟수</div>
    <div class="group_form w-100">소진한 연차</div>
</div>
<talbe class="bottom_group">
    <?
    if ($stmt->rowCount() > 0){
        while($idrow=$stmt->fetch(PDO::FETCH_ASSOC)){
            if($idrow['activate']==1){
                echo '<div class="group_wrap w-'.$width_control_array[$width_array_con].'00">';
                echo '<div class="group_form w-200 center">'.$idrow['userprofile'].'</div>';
                try {
                    $vacstmt = $con->prepare('SELECT * FROM vacation WHERE uid=:uid and va_day_start>=:output_start_day and va_day_start<=:output_end_day ORDER BY va_day_start asc');
                    $vacstmt->bindParam(':output_start_day',$output_start_day);
    				$vacstmt->bindParam(':output_end_day',$output_end_day_plus);
                    $vacstmt->bindParam(':uid',$idrow['uid']);
                    $vacstmt->execute();
                } 
                catch(PDOException $e) {
                    die("Database error. " . $e->getMessage()); 
                }           
                $haveday = 0;
                $usage_count_all = 0;
                $usage_date_all = 0;
                $usage_count_index = array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0');
                $usage_count_detail = array('','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
                if ($vacstmt->rowCount() > 0){
                    while($vacrow=$vacstmt->fetch(PDO::FETCH_ASSOC)){
                        extract($vacrow);$holiday_index = 0;
                        $year = date('Y', strtotime($vacrow['va_day_start']));
                        $holiday_array = array('',$year.'-01-01',$year.'-02-04',$year.'-02-05',$year.'-02-06',$year.'-03-01',$year.'-05-05',$year.'-05-12',$year.'-06-06',$year.'-08-15',$year.'-09-12',$year.'-09-13',$year.'-09-14',$year.'-10-03',$year.'-10-09',$year.'-12-25');
                        $holiday_index = array_search($vacrow['va_day_start'], $holiday_array);
                        $day_of_the_week = 0;
                        $day_of_the_week_array = array("1","0","0","0","0","0","1");
                        $day_of_the_week = ($day_of_the_week_array[date('w', strtotime($vacrow['va_day_start']))]);

                        if($holiday_index == 0 and $day_of_the_week == 0 and $vacrow['activate']== 1 ){
                        	if($vacrow['va_case']==1){ $usage_date_all++;}
                        	else if($vacrow['va_case']==2){ $usage_date_all=$usage_date_all+0.5;}
                        	else if($vacrow['va_case']==3){ $usage_date_all=$usage_date_all+0.25;}

                            $usage_diff_va_day_con = date("y",strtotime($vacrow['va_day_start']))*12 + date("n",strtotime($vacrow['va_day_start']));
                            $usage_diff_output_start_con = date("y",strtotime($output_start_day))*12 + date("n",strtotime($output_start_day));
                            $usage_diff_months = $usage_diff_va_day_con - $usage_diff_output_start_con;

                            $usage_count_index[$usage_diff_months]+1;
                            $usage_count_index_con = $usage_count_index[$usage_diff_months];
                            $usage_count_index_con = $usage_count_index_con+1;
                            $usage_count_index[$usage_diff_months] = $usage_count_index_con;
                            ++$usage_count_all;

                            $usage_count_detail[$usage_diff_months] .= $vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];
                        }
                    } 
                }
                for ($i=0; $i < $diff_months_all; $i++) { //출력부분 ?> 
                    <div class="group_form w-100">

                        	<? 
                        	if ($usage_count_index[$i]!=0){
                        	echo '<div class="usage_count_point"><span>'.$usage_count_index[$i].'</span><p class="arrow_box">'.$usage_count_detail[$i].'</p></div>' ;
                        	}
                        	else{
                        		echo "-";
                        	}?>

                    </div>
                <? }
                echo '<div class="group_form w-100">'.$usage_count_all.'</div>';
                echo '<div class="group_form w-100">'.$usage_date_all.'</div>';
                echo '</div>';
            }
        }
    }?>  
</talbe>