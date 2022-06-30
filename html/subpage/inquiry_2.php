<?php 
$first_beginning = '2015-1-1';
$diff = abs(strtotime($first_beginning) - strtotime($output_end_day));
$diff_years = floor($diff / (365*60*60*24)+1);
$inquiry_wrap_1_output_year = date("Y",strtotime($first_beginning));

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

$width_control_array = array('','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20');

$width_array_con = 0; 
for ($i=0; $i < $diff_years; $i++) { $width_array_con++; } //하단 출력 폼 width 값 조정용
?>

<div class="inquiry_date_con"></div>
<?
if ($idrow['authority']==2) {
    $stmt = $con->prepare('SELECT * FROM users ORDER BY startday asc');
    echo "전체 팀원의 ";
}
else if ($idrow['authority']==1) {
    $stmt = $con->prepare('SELECT * FROM users WHERE team=:team ORDER BY startday asc');
    $stmt->bindParam(':team',$idrow['team']);
    $array_team =  array("none","마케팅","VOD","totur","VI","CX","B2B","other");
    echo $array_team[$idrow['team']]."팀원의 ";
}
echo "휴가 사용 횟수를 각 년별로 조회합니다.  (2015~현재년도)";
$stmt->execute();
$output_end_day_plus = $output_end_day;
$output_end_day_plus++;
?>
<div class="top_group w-<?php echo $width_control_array[$width_array_con]?>00">
    <div class="group_form w-200"></div>
    <?php 
    for ($i=0; $i < $diff_years; $i++) { ?>
        <div class="group_form w-100">
            <div class=""><?php echo $inquiry_wrap_1_output_year++;?>년 </div>
        </div>
    <?php }?>
</div>
<div class="bottom_group">
    <?php
    if ($stmt->rowCount() > 0){
        while($idrow=$stmt->fetch(PDO::FETCH_ASSOC)){
            if($idrow['activate']==1){
                echo '<div class="group_wrap w-'.$width_control_array[$width_array_con].'00">';
                echo '<div class="group_form w-200 center">'.$idrow['userprofile'].'</div>';
                try { //여부분 만들자
                    $vacstmt = $con->prepare('SELECT * FROM vacation WHERE uid=:uid ORDER BY va_day_start asc');

                    $vacstmt->bindParam(':uid',$idrow['uid']);
                    $vacstmt->execute();
                } 
                catch(PDOException $e) {
                    die("Database error. " . $e->getMessage()); 
                }
                $usage_count_index = array('0','0','0','0','0','0','0','0','0','0','0','0');
                $usage_count_detail = array('','','','','','','','','','','','','');
                if ($vacstmt->rowCount() > 0){
                    while($vacrow=$vacstmt->fetch(PDO::FETCH_ASSOC)){
                        extract($vacrow);
                        $holiday_index = 0;
                        $year = date('Y', strtotime($vacrow['va_day_start']));
                        $holiday_array = array('',$year.'-01-01',$year.'-02-04',$year.'-02-05',$year.'-02-06',$year.'-03-01',$year.'-05-05',$year.'-05-12',$year.'-06-06',$year.'-08-15',$year.'-09-12',$year.'-09-13',$year.'-09-14',$year.'-10-03',$year.'-10-09',$year.'-12-25');
                        $holiday_index = array_search($vacrow['va_day_start'], $holiday_array);
                        $day_of_the_week = 0;
                        $day_of_the_week_array = array("1","0","0","0","0","0","1");
                        $day_of_the_week = ($day_of_the_week_array[date('w', strtotime($vacrow['va_day_start']))]);
                        if($holiday_index == 0 and $day_of_the_week == 0 and $vacrow['activate']== 1 ){                    	 
                            $usage_diff_con_first_beginning = date("Y",strtotime($first_beginning));
                            $usage_diff_con_va_day_start = date("Y",strtotime($vacrow['va_day_start']));
                        	$usage_diff = ($usage_diff_con_va_day_start - $usage_diff_con_first_beginning);


                            $usage_count_index_con = $usage_count_index[$usage_diff];
                            $usage_count_index_con = $usage_count_index_con+1;
                            $usage_count_index[$usage_diff] = $usage_count_index_con;
                            $usage_count_detail[$usage_diff] .= $vacrow['va_day_start'].' '.$array_vac_case[$vacrow['va_case']];
                        }
                    }            	
                	//지금-입사일 차이 연산
                	$nowday = date('Y-m-d'); 
    				$diff = abs(strtotime($nowday) - strtotime($idrow['startday']));
    				$diffyears = floor($diff / (365*60*60*24));
    				$diffmonth = floor(($diff - $diffyears * 365*60*60*24) / (30*60*60*24));

    				$vac_day_form_word_years = array('','15','15','16','16','17','17','17','17','17','19','20','20','20','20','20','20','20','20','20','24','25','25','25','25','25','25');
                }
                $i=0;
                for ($i=0; $i < $diff_years; $i++) { //출력부분 ?> 
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
                echo '</div>';
            }
        }
    }
    ?>  
</div>