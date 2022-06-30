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

$width_control_array = array('','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20');

$width_array_con = 0; 
for ($i=0; $i < $diff_years; $i++) { $width_array_con++; } //하단 출력 폼 width 값 조정용

if ($idrow['authority']==2) {
    $stmt = $con->prepare('SELECT * FROM users ORDER BY startday asc');
    echo "전체 팀원의 최근 휴가 히스토리를 조회합니다";
}
else if ($idrow['authority']==1) {
    $stmt = $con->prepare('SELECT * FROM users WHERE team=:team ORDER BY startday asc');
    $stmt->bindParam(':team',$idrow['team']);
    $array_team =  array("none","마케팅","VOD","totur","VI","CX","B2B","other");
    echo $array_team[$idrow['team']]."팀원의 최근 휴가 히스토리를 조회합니다.";
}
$idrow = $stmt->fetch();  
?>

<?php  
try { //여부분 만들자
    $stmt = $con->prepare('SELECT * FROM vacation ORDER BY correction_time desc');
    $stmt->execute();
} 
catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$year = date('Y');
$holiday_array = array('',$year.'-1-1',$year.'-2-4',$year.'-2-5',$year.'-2-6',$year.'-3-1',$year.'-5-5',$year.'-5-12',$year.'-6-6',$year.'-8-15',$year.'-9-12',$year.'-9-13',$year.'-9-14',$year.'-10-3',$year.'-10-9',$year.'-12-25');
$array_vac_case = array("","연차","반차","반반차","대체휴가","반대체휴가","반반대체휴가","공가","생일휴가","기타");


if ($stmt->rowCount() > 0){?>
    <div class="user_usage_inquiry">
        <div class="sub_check">
            <div class="top_group">
                <div class="group_form w-100 border-top-left-5 border-top-right-5">상태</div>
                <div class="group_form w-50 border-top-left-5 border-top-right-5">이름</div>
                <div class="group_form w-200 border-top-left-5 border-top-right-5">날짜</div>
                <div class="group_form w-100 border-top-left-5 border-top-right-5">종류</div>
                <div class="group_form w-200 border-top-left-5 border-top-right-5">최초등록시간</div>
                <div class="group_form w-200 border-top-left-5 border-top-right-5">삭제시간</div>
            </div>
        </div>
        <?php 
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $holiday_index = 0;
            $year = date('Y', strtotime($row['va_day_start']));
            $holiday_array = array('',$year.'-01-01',$year.'-02-04',$year.'-02-05',$year.'-02-06',$year.'-03-01',$year.'-05-05',$year.'-05-12',$year.'-06-06',$year.'-08-15',$year.'-09-12',$year.'-09-13',$year.'-09-14',$year.'-10-03',$year.'-10-09',$year.'-12-25');
            $holiday_index = array_search($row['va_day_start'], $holiday_array);
            $day_of_the_week = 0;
            $day_of_the_week_array_con = array("1","0","0","0","0","0","1");
            $day_of_the_week_array = array("일","월","화","수","목","금","토");
            $day_of_the_week = ($day_of_the_week_array_con[date('w', strtotime($row['va_day_start']))]);
            $activate_array = array('<div class="group_form w-100 color-red">삭제됨</div>','<div class="group_form w-100  color-blue">활성화</div>');
            if($holiday_index == 0 and $day_of_the_week == 0){ ?>
            <div class="group_wrap w-850">
                <?php echo $activate_array[$row['activate']];?> 
                <div class="group_form w-50"><?php echo $row['userprofile'];?> </div>
                <div class="group_form w-200"><?php echo $row['va_day_start'];?><?php echo '('.$day_of_the_week_array[date('w', strtotime($row['va_day_start']))].') </div>'; ?>
                <div class="group_form w-100">
                    <?
                    if ($row['va_case'] == 1 or $row['va_case'] == 4){  }

                    //반차 오전 오후 표시
                    if ($row['va_case'] == 2 or $row['va_case'] == 5){  
                        if($row['va_day_detail'] == '10:00:00'){echo '오전';}
                        else if($row['va_day_detail'] == '15:00:00'){echo '오후';}
                    }
                    echo $array_vac_case[$row['va_case']];
                    //공가 or 기타 사유 표시
                    if ($row['va_case'] == 7 or $row['va_case'] == 9){  
                        echo ' ('.$row['detail'].')';
                    }?>
                </div>
                <div class="group_form w-200"><?php echo $row['vac_regtime'];?> </div>

                <div class="group_form w-200"><?php if($row['correction_time']!=$row['vac_regtime']){echo $row['correction_time'];}else{echo "<div class='group_form w-200'>-</div>";}?> </div>

            </div>
            <?php }
        }?>
    </div>
<?php } ?>  
