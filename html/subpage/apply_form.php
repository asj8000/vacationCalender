<?php 
try { 
    $session_userid=$_SESSION['user_id'];
    $stmt = $con->prepare('select * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
   
} catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$row = $stmt->fetch();  



if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])){

    $getdevide = 0;
    $getsort = 0;

    $getdevide = $_POST['devide_select'];
    $getsort_1=$_POST['sort_1'];
    $getsort_2=$_POST['sort_2'];
    $getsort_3=$_POST['sort_3'];


    $getdetail = $_POST['detail_insert_form'];
    if($getdevide==1){
        $getsort = $getsort_1;
    }
    else if($getdevide==2){
        $getsort = $getsort_2;
    }
    else if($getdevide==3){
        $getsort = $getsort_3;
    }
    else{
        $getsort = 0;
        $errMSG = "종류 선택 오류";
    }

   
    $multiple_data = 0;
    
    if($getsort==1 or $getsort==4){
        if(isset($_POST['c1'])){
            $va_day_detail = null;
            $detail = null;
            $multiple_data_start = $_POST['start_date_1'];
            $multiple_data_end = $_POST['end_date'];
            $multi_con_diff = abs(strtotime($multiple_data_end) - strtotime($multiple_data_start));
            $multi_con_diffyears = floor($multi_con_diff / (365*60*60*24));
            $multi_con_diffmonth = floor(($multi_con_diff - $multi_con_diffyears * 365*60*60*24) / (30*60*60*24));
            $multi_con_diffdays = floor(($multi_con_diff - $multi_con_diffyears * 365*60*60*24 - $multi_con_diffmonth*30*60*60*24)/ (60*60*24));
            $multiple_data = $multi_con_diffdays + 1;
        }
        else{
            $va_day_start = $_POST['start_date_0'];
            $va_day_detail = null;
            $detail = null;
        }
    }
    else if($getsort == 2 or $getsort == 5 or $getsort == 3 or $getsort == 6){
        $va_day_start = $_POST['start_date_2'];
        $va_day_detail = $_POST['select_ampm'];
        $detail = null;
    }
    else if($getsort == 8){
        $va_day_start = $_POST['start_date_4'];
        $va_day_detail = null;
        $detail = null;
    }
    else if($getsort == 7 or $getsort == 9){
        $va_day_start = $_POST['start_date_5'];
        $va_day_detail = null;
        $detail = $getdetail;
    }
    else{
        $errMSG = '오류가 발생했습니다';
    }
    if($va_day_start<=$row['startday']){
        $errMSG = '입사일보다 이른 때에 휴가를 등록할 수 없습니다.';
    }
    if(!isset($errMSG)){
        try{
            $time = date("Y-m-d H:i:s");
            $stmt = $con->prepare('INSERT INTO vacation
                (uid, va_case, userprofile, va_day_start, va_day_detail, detail ,correction_time) VALUES
                (:uid, :va_case, :userprofile, :va_day_start, :va_day_detail, :detail, :correction_time)');
            $stmt->bindParam(':uid',$row['uid']);
            $stmt->bindParam(':va_case',$getsort);
            $stmt->bindParam(':userprofile',$row['userprofile']);
            $stmt->bindParam(':va_day_start',$va_day_start);
            $stmt->bindParam(':va_day_detail',$va_day_detail);
            $stmt->bindParam(':detail',$detail);
            $stmt->bindParam(':correction_time',$time);
        
            if($stmt->execute()){
                $message = '등록이 완료되었습니다.';
                echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$message');
                    window.location.replace(\"apply.php\");
                </SCRIPT>";
            }
            else{
                $errMSG = "오류가 발생했습니다";
                echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$errMSG');
                    window.location.replace(\"apply.php\");
                </SCRIPT>";
            }
        } 
        catch(PDOException $e) {
            die("Database error: " . $e->getMessage()); 
        }
    } 
    else{
        echo "<SCRIPT type='text/javascript'> //not showing me this
            alert('$errMSG');
            window.location.replace(\"apply.php\");
        </SCRIPT>"; 
    } 
}
?>

<div class="apply_wrap">
    <div class="apply_background"></div>
    <form method="POST" id="form"> 
        <div id="apply_form" class="apply_form">
            <h2>휴가 신청하기</h2>
            <div class="devide_form">
                구분 : 
                <select name="devide_select" onChange="change_devide(this.options[this.selectedIndex].value)">
                    <option value="0"></option>
                    <option value="1">연차</option>
                    <option value="2">대체휴가</option>
                    <option value="3">기타</option>
                </select>
            </div>
            <div class="sort_form">
                <div id=sort_1 style="display:none;">
                    종류 : 
                    <select name="sort_1" onChange="change_sort(this.options[this.selectedIndex].value)">
                        <option value="0"></option>
                        <option value="1">연차(8h,1일)</option>
                        <option value="2">반차(4h,0.5일)</option>
                        <option value="3">반반차(2h,0.25일)</option>
                    </select>
                    <input type="hidden" name="" value="newusername" /> 
                </div>
                <div id=sort_2 style="display:none;">
                    종류 : 
                    <select name="sort_2" onChange="change_sort(this.options[this.selectedIndex].value)">
                        <option value="0"></option>
                        <option value="4">휴가(8h,1일)</option>
                        <option value="5">반휴가(4h,0.5일)</option>
                        <option value="6">반반휴가(2h,0.25일)</option>
                    </select>
                </div>
                <div id=sort_3 style="display:none;">
                    종류 : 
                    <select name="sort_3" onChange="change_sort(this.options[this.selectedIndex].value)">
                        <option value="0"></option>
                        <option value="7">공가</option>
                        <option value="8">생일휴가</option>
                        <option value="9">기타</option>
                    </select>
                </div>                          
            </div>
            <div id="detail_wrap" style="display:none;">
                <div class="date_insert_form">
                    <div id=dete_insert_1 style="display:none;"><!--1,4-->
                        <!--<div class="dete_insert_check_box">
                            <input type="checkbox" id="c1" name="cc" />
                            <label for="c1"><span></span>휴가 여러일 사용하기</label>
                        </div>-->
                        <div id=dete_insert_1_0 style="display:inline;">
                        사용일 : <input type="date" name="start_date_0" value="<?php echo $nowday?>">
                        </div>
                        <div id=dete_insert_1_1 name="" style="display:none;">
                        시작일 : <input type="date" name="start_date_1" value="<?php echo $nowday?>"> 
                        ~ 종료일 <input type="date" name="end_date" value="<?php echo $nowday?>">
                        </div>
                    </div>
                    <div id=dete_insert_2 style="display:none;"><!--2,3,5,6-->
                        사용일 : <input type="date" name="start_date_2" value="<?php echo $nowday?>">
                        <select name="select_ampm">
                            <option value="1">오전</option>
                            <option value="2">오후</option>
                        </select>
                    </div>
                    <div id=dete_insert_3 style="display:none;"><!--8-->
                        사용일 : <input type="date" name="start_date_4" value="<?php echo $nowday?>">
                    </div>
                    <div id=dete_insert_4 style="display:none;"><!--7,9-->
                        사용일 : <input type="date" name="start_date_5" value="<?php echo $nowday?>">
                    </div>
                </div>
                <div class="detail_form" id="dete_insert_0" style="display:none;">
                    설명 : <input type="" name="detail_insert_form" id="detail_form" value="">
                </div>
                <!--<div class="preview">
                    <div class="f-left">
                        미리보기 :
                    </div>
                    <div class="f-right event">
                        <div class="event-desc">
                            <?php echo '['.$row['userprofile'].'] ';?>
                            <div id="out"></div>
                        </div>
                    </div>
                </div>-->
                <div class="submit_button_form">

                    <td colspan="2" align="center">
                    <button type="submit" name="submit" class="button_effect_0 button_1" >등록</button>
                    </td>
                </div> 
            </div>
        </div>
    </form>
</div>
<SCRIPT LANGUAGE="JavaScript">
function change_devide(style) {
    if(style=="0"){
        detail_wrap.style.display = "none"
        sort_1.style.display = "none"
        sort_2.style.display = "none"
        sort_3.style.display = "none"
    } 
    if(style=="1"){
        detail_wrap.style.display = "none"
        sort_1.style.display = "inline"
        sort_2.style.display = "none"
        sort_3.style.display = "none"
    }
    if(style=="2"){
        detail_wrap.style.display = "none"
        sort_1.style.display = "none"
        sort_2.style.display = "inline"
        sort_3.style.display = "none"
    }
    if(style=="3"){
        detail_wrap.style.display = "none"
        sort_1.style.display = "none"
        sort_2.style.display = "none"
        sort_3.style.display = "inline"
    }
}
function change_sort(style) {
    if(style=="0"){
        detail_wrap.style.display = "none"
        dete_insert_0.style.display = "none"
        dete_insert_1.style.display = "none"
        dete_insert_2.style.display = "none"
        dete_insert_3.style.display = "none"
        dete_insert_4.style.display = "none"
    } 
    if(style=="1" || style=="4"){
        detail_wrap.style.display = "inline"
        dete_insert_0.style.display = "none"
        dete_insert_1.style.display = "inline"
        dete_insert_2.style.display = "none"
        dete_insert_3.style.display = "none"
        dete_insert_4.style.display = "none"
    }
    if(style=="2" || style=="5" || style=="3" || style=="6"){
        detail_wrap.style.display = "inline"
        dete_insert_0.style.display = "none"
        dete_insert_1.style.display = "none"
        dete_insert_2.style.display = "inline"
        dete_insert_3.style.display = "none"
        dete_insert_4.style.display = "none"
    }
    if(style=="8"){
        detail_wrap.style.display = "inline"
        dete_insert_0.style.display = "none"
        dete_insert_1.style.display = "none"
        dete_insert_2.style.display = "none"
        dete_insert_3.style.display = "inline"
        dete_insert_4.style.display = "none"
    }
    if(style=="7" || style=="9"){
        detail_wrap.style.display = "inline"
        dete_insert_0.style.display = "inline"
        dete_insert_1.style.display = "none"
        dete_insert_2.style.display = "none"
        dete_insert_3.style.display = "none"
        dete_insert_4.style.display = "inline"
    }
}
</SCRIPT>
<script>
    $(document).ready(function(){
        $(".on_apply_popup").click(function(){
            $(".apply_wrap").toggle();
        });
        $(".apply_background").click(function(){
            $(".apply_wrap").toggle();
        });
    });

    $(document).ready( function() {
        $('#c1').click( function() {
            $('#dete_insert_1_0').toggle('');
            $('#dete_insert_1_1').toggle('slow');
        });
    });
</script>
<script type="text/javascript">
$(document).ready(function(){
    $("#detail_form").keyup(function(){
        $("#out").text($("#data").val());
    });
});
</script>