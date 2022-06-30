<?php  
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
$id_row = $stmt->fetch();  

try { 
    $stmt = $con->prepare('SELECT * FROM vacation WHERE uid=:uid ORDER BY va_day_start desc');
    $stmt->bindParam(':uid',$uid);
    $stmt->execute();
} 
catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}

$array_vac_case = array("","연차","반차","반반차","대체휴가","반대체휴가","반반대체휴가","공가","생일휴가","기타");


if ($stmt->rowCount() > 0){?>
    <h1>사용한 모든 휴가 내역</h1>
<div class="user_usage_inquiry">
    <div class="waring">
        <div class="guide_word">아직 지나지 않았거나 등록한지 3일이 지나지 않은 휴가만 삭제 가능합니다.</div>
    </div>
    <div class="sub_check">
        <div class="top_group">
            <div class="group_form w-200 border-top-left-5 border-top-right-5">사용일</div>
            <div class="group_form w-100 border-top-left-5 border-top-right-5">휴가종류</div>
            <div class="group_form w-100 border-top-left-5 border-top-right-5">삭제하기</div>
        </div>
    </div>
    <?php 
    $only_one_active = 1;
    //지금-입사일 차이 연산
    $diff = abs(strtotime($nowday) - strtotime($id_row['startday']));
    $diffyears = floor($diff / (365*60*60*24));
    $diffmonth = floor(($diff - $diffyears * 365*60*60*24) / (30*60*60*24));
    $diffdays = floor(($diff - $diffyears * 365*60*60*24 - $diffmonth*30*60*60*24)/ (60*60*24));

    $array_vac_case = array("","연차","반차","반반차","대체휴가","반대체휴가","반반대체휴가","공가","생일휴가","기타");

    $count_used_case =  array('0','0','0','0','0','0','0','0','0','0');

    $start_year = date("Y",strtotime($id_row['startday']));
    $start_month = date("m",strtotime($id_row['startday']));
    $start_day =date("d",strtotime($id_row['startday']));
    //총 지급된 휴가 개수
    if ($diffyears>=2) { $start_date_of_used = $start_year + $diffyears.'-'.$start_month.'-'.$start_day;}
    else if ($diffyears<=1) { $start_date_of_used = $start_year.'-'.$start_month.'-'.$start_day;}
    else if ($diffyears>=0) {}
      
    while($vacrow=$stmt->fetch(PDO::FETCH_ASSOC)){
        $year = date('Y', strtotime($vacrow['va_day_start']));
        $holiday_array = array('',$year.'-01-01',$year.'-02-04',$year.'-02-05',$year.'-02-06',$year.'-03-01',$year.'-05-05',$year.'-05-12',$year.'-06-06',$year.'-08-15',$year.'-09-12',$year.'-09-13',$year.'-09-14',$year.'-10-03',$year.'-10-09',$year.'-12-25');
        $holiday_index = 0;
        $holiday_index = array_search($vacrow['va_day_start'], $holiday_array);
        $day_of_the_week = 0;
        $day_of_the_week_array_con = array("1","0","0","0","0","0","1");
        $day_of_the_week_array = array("일","월","화","수","목","금","토");
        $day_of_the_week = ($day_of_the_week_array_con[date('w', strtotime($vacrow['va_day_start']))]);
        if($holiday_index == 0 and $day_of_the_week == 0 and $vacrow['activate']== 1 ){ ?>
            <div class="bottom_group">
                <?
                if ($only_one_active==1 and $vacrow['va_day_start'] < $start_date_of_used) { ?>
                    <div class="bottom_group"></div>
                    <div class="bottom_group">과거에 사용한 휴가 내역들</div>
                    <?$only_one_active = 0; 
                }?>
                <div class="group_form w-200"><?php echo $vacrow['va_day_start']?><?php echo '('.$day_of_the_week_array[date('w', strtotime($vacrow['va_day_start']))].') '; ?></div>
                <div class="group_form w-100">
                    <?
                    //반차 오전 오후 표시
                    if ($vacrow['va_case'] == 2 or $vacrow['va_case'] == 5 or $vacrow['va_case'] == 3 or $vacrow['va_case'] == 5){  
                        if($vacrow['va_day_detail'] == '10:00:00'){echo '오전';}
                        else if($vacrow['va_day_detail'] == '15:00:00'){echo '오후';}
                    }
                    echo $array_vac_case[$vacrow['va_case']];
                    //공가 or 기타 사유 표시
                    if ($vacrow['va_case'] == 7 or $vacrow['va_case'] == 9){  
                        echo ' ('.$vacrow['detail'].')';
                    }?>
                </div>
                <div class="group_form w-100">
                    <? //삭제하기 폼
                    $nowday = date('Y-m-d'); 
                    $check_day = date("Y-m-d", strtotime($vacrow['vac_regtime']));
                    $check_diff = abs(strtotime($nowday) - strtotime($check_day));
                    if ($check_diff<3 or $vacrow['va_day_start']>=$nowday ) {?>
                        <a class="btn btn-primary" href="subpage/delete_vac.php?del_vac=<?php echo $vacrow['vacid'] ?>" onclick="return confirm('이 휴가내역을 삭제하시겠습니까?\n삭제시 히스토리가 남게됩니다.');"><span class="glyphicon glyphicon-pencil"></span>삭제</a>
                    <?}
                    else {?>
                        <a> - </a>
                    <?}?>
                </div>
        	</div>
        <?php }
    }?>
</div>
<?php } ?>  

