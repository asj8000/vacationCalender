<?php //달력 출력용 연산
    date_default_timezone_set('Asia/Seoul');
    //오늘 날짜
    $nowyear = date('Y');
    $nowmonth = date('n');
    $today = date('j');

    //출력할 날짜
    $year = isset($_GET['year']) ? $_GET['year'] : $nowyear; 
    $month = isset($_GET['month']) ? $_GET['month'] : $nowmonth;
    $day = isset($_GET['day']) ? $_GET['day'] : $today;

    //리모컨 셋팅
    $prev_month = $month - 1;
    $next_month = $month + 1;
    $prev_year = $next_year = $year;
    if ($month == 1) {
        $prev_month = 12;
        $prev_year = $year - 1;
    } else if ($month == 12) {
        $next_month = 1;
        $next_year = $year + 1;
    }
    $prevyear = $year - 1;
    $nextyear = $year + 1;
    $prevdate = date("Y-m-d", mktime(0, 0, 0, $month - 1, 1, $year));
    $nextdate = date("Y-m-d", mktime(0, 0, 0, $month + 1, 1, $year));

    //월 영어로 출력 
    $array_month_eng= array("","Janyary","February","March","April","May","June","July","August","September","October","November","December");
?>

<div id="calendar-wrap">
    <div class="calendar_header">
        <div class="calendar_control_header f-left"><?php echo "$array_month_eng[$month]"; ?>  <?php echo "$year"; ?></div>
        <form class="calendar_control_nav f-right">
            <td><a href=<?php echo 'index.php?year='.$prevyear.'&month='.$month . '&day=1'; ?>> << </a></td>
            <td><a href=<?php echo 'index.php?year='.$prev_year.'&month='.$prev_month . '&day=1'; ?>> < </a></td>
            <td height="50" bgcolor="#FFFFFF" colspan="3"><a href=<?php echo 'index.php?year=' . $nowyear . '&month=' . $nowmonth . '&day=1'; ?>>today</a></td>
            <td><a href=<?php echo 'index.php?year='.$next_year.'&month='.$next_month.'&day=1'; ?>> > </a></td>
            <td><a href=<?php echo 'index.php?year='.$nextyear.'&month='.$month.'&day=1'; ?>> >> </a></td>
        </form>
    </div>
    <div class="calendar">
        <ul class="weekdays">
            <li>Sunday</li>
            <li>Monday</li>
            <li>Tuesday</li>
            <li>Wednesday</li>
            <li>Thursday</li>
            <li>Friday</li>
            <li>Saturday</li>
            <div class="null_form"></div>
        </ul>
        <div class="days_form">
            <?php
            $day=1;
            $before_end=1;
            $after_end=0;
            $after_day=1;

            $max_day = date('t', mktime(0, 0, 0, $month, 1, $year));
            $start_week = date("w", mktime(0, 0, 0, $month, 1, $year));
            $total_week = ceil(($max_day + $start_week) / 7);
            $last_week = date('w', mktime(0, 0, 0, $month, $max_day, $year));

            $before_month = $month - 1;
            $after_month = $month + 1;
            $before_max_day = date('t', mktime(0, 0, 0, $before_month, 1, $year));

            $holiday_array = array('',$year.'-1-1',$year.'-2-4',$year.'-2-5',$year.'-2-6',$year.'-3-1',$year.'-5-5',$year.'-5-12',$year.'-6-6',$year.'-8-15',$year.'-9-12',$year.'-9-13',$year.'-9-14',$year.'-10-3',$year.'-10-9',$year.'-12-25');
            $holiday_detail = array('','새해','설날연휴','설날','설날연휴','3.1 운동','어린이날','부처님 오신 날','현충일','광복절','추석연휴','추석','추석연휴','개천절','한글날','성탄절');
            $array_vac_case = array("","연차","반차","반반차","대체휴가","반휴가","반반휴가","공가","생일휴가","기타");
            $before_day = $before_max_day - $start_week + 1;

            for($i=1; $i <= $total_week; $i++){ ?>
                <ul class="days">
                    <?php 
                    for ($j = 0; $j < 7; $j++) {
                        if (!(($i == 1 && $j < $start_week) || ($i == $total_week && $j > $last_week))) { ?>
                            <li class="day <?php if(isset($nowyear))?>">
                            <?php
                            $chk = $year.'-'.$month.'-'.$day;
                            if ($j == 0) { $day_color = "holy"; } 
                            else if(in_array($chk, $holiday_array)){ $day_color = "holy"; }
                            else if ($j == 6) { $day_color = "blue"; } 
                            else { $day_color = "white"; }
                            if ($year == $nowyear and $month == $nowmonth and $day == date("j")) {
                                echo '<div class="date center '.$day_color.' today">'.$day.'</div>';
                            } 
                            else {
                                echo '<div class="date center '.$day_color.'">'.$day.'</div>';
                            }
                            $before_end=0;
                            if ($day==$max_day) {
                                $after_end=1;
                            }
                            if($day <= 9){
                                $day = '0'+ $day;
                            }
                        }
                        else if($before_end==1){ ?>
                            <li class="day <?php if(isset($nowyear)){echo'other-month';} ?>">
                            <?php
                            $before_month = $month - 1;
                            $before_year = $year;
                            if($before_month <= 0){
                                $before_month = 12;
                                $before_year = $year - 1;
                            }
                            $chk = $before_year.'-'.$before_month.'-'.$before_day;
                            if ($j == 0) { $day_color = "holy"; } 
                            else if(in_array($chk, $holiday_array)){ $day_color = "holy"; }
                            else if ($j == 6) { $day_color = "blue"; } 
                            else { $day_color = "white"; }
                            if ($year == $nowyear and $month == $nowmonth and $day == date("j")) {
                                echo '<div class="date center '.$day_color.' today">'.$before_day.'</div>';
                            } 
                            else {
                                echo '<div class="date center '.$day_color.' ">'.$before_day.'</div>';
                            }
                            $before_day++;
                        }
                        else if($after_end==1){ ?>
                            <li class="day <?php if(isset($nowyear)){echo'other-month';} ?>">
                            <?php
                            $after_month = $month + 1;
                            $after_year = $year;
                            if($after_month >= 13){
                                $after_month = 1;
                                $after_year = $year + 1;
                                $year++;
                                $holiday_array = array('',$year.'-1-1',$year.'-2-4',$year.'-2-5',$year.'-2-6',$year.'-3-1',$year.'-5-5',$year.'-5-12',$year.'-6-6',$year.'-8-15',$year.'-9-12',$year.'-9-13',$year.'-9-14',$year.'-10-3',$year.'-10-9',$year.'-12-25');
                            }
                            $chk = $after_year.'-'.$after_month.'-'.$after_day;
                            if ($j == 0) { $day_color = "holy"; } 
                            else if(in_array($chk, $holiday_array)){ $day_color = "holy"; }
                            else if ($j == 6) { $day_color = "blue"; } 
                            else { $day_color = "white"; }
                            if ($year == $nowyear and $month == $nowmonth and $day == date("j")) {
                                echo '<div class="date center '.$day_color.' today">'.$after_day.'</div>';
                            } 
                            else {
                                echo '<div class="date center '.$day_color.' ">'.$after_day.'</div>';
                            }
                            $after_day++;
                        }
                        $holiday_index = 0;
                        $holiday_index = array_search($chk, $holiday_array);
                        if($holiday_index != 0){
                        echo '<div class="holy_detail">'.$holiday_detail[$holiday_index].'</div>';}

                        $stmt = $con->prepare('SELECT * FROM vacation WHERE va_day_start=:chk ORDER BY va_day_start asc');
                        $stmt->bindParam(':chk',$chk);
                        $stmt->execute();
                        if ($stmt->rowCount() > 0){
                            while($row=$stmt->fetch()){
                                if(in_array($chk, $holiday_array)){}
                                else{
                                    if($row['activate']){?>
                                        <div class="event"> 
                                            <div class="event-desc"> 
                                                <?php 
                                                try { 
                                                    $uid_stmt = $con->prepare('SELECT * FROM users WHERE uid=:uid');
                                                    $uid_stmt->bindParam(':uid',$row['uid']);
                                                    $uid_stmt->execute();
                                                } 
                                                catch(PDOException $e) {
                                                    die("Database error. " . $e->getMessage()); 
                                                }
                                                $uidrow = $uid_stmt->fetch();
                                                if(isset($uidrow['userprofile'])){
                                                    echo '['.$uidrow['userprofile'].'] ';
                                                }
                                                else{
                                                    echo '['.$row['userprofile'].'] ';
                                                }
                                                //반차 오전 오후 표시
                                                if ($row['va_case'] == 2 or $row['va_case'] == 5 or $row['va_case'] == 3 or $row['va_case'] == 6){  
                                                    if($row['va_day_detail'] == '1'){echo '오전';}
                                                    else if($row['va_day_detail'] == '2'){echo '오후';}
                                                }
                                                echo $array_vac_case[$row['va_case']];
                                                //공가 or 기타 사유 표시
                                                if ($row['va_case'] == 7 or $row['va_case'] == 9){  
                                                    echo ' ('.$row['detail'].')';
                                                }?>
                                            </div>
                                        </div>  
                                        <?php
                                    }
                                }
                            }
                        }
                        if($before_end==0 and $after_end==0){$day++;}?>
                    </li>
                <?php }?>
                </ul>
            <?php } ?>
        </div>
    </div>
</div>