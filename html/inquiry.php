<?
include('subpage/dbcon.php'); 
include('subpage/check.php');

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
$userauthority=$idrow['authority'];

if (is_login()){
    if ($userauthority>=1);
    else
        header("Location: index.php");
}
else
    header("Location: welcome.php"); 

date_default_timezone_set('Asia/Seoul');
//현재 날자
$nowyear = date('Y');
$nowmonth = date('n');

$thisday = $nowyear.'-'.$nowmonth;
$one_y_before_day = --$nowyear.'-'.$nowmonth;

$output_start_day = isset($_GET['first_day']) ? $_GET['first_day'] : $one_y_before_day; 
$output_end_day = isset($_GET['last_day']) ? $_GET['last_day'] : $thisday;

$holiday_array = array('',$nowyear.'-1-1',$nowyear.'-2-4',$nowyear.'-2-5',$nowyear.'-2-6',$nowyear.'-3-1',$nowyear.'-5-5',$nowyear.'-5-12',$nowyear.'-6-6',$nowyear.'-8-15',$nowyear.'-9-12',$nowyear.'-9-13',$nowyear.'-9-14',$nowyear.'-10-3',$nowyear.'-10-9',$nowyear.'-12-25');
$array_vac_case = array("","연차","반차","반반차","대체휴가","반대체휴가","반반대체휴가","공가","생일휴가","기타");

$view_page = isset($_GET['view_page']) ? $_GET['view_page'] : 1;
?>

<!DOCTYPE html>
<html>
<head>
    <title>조회 - 휴가테이블</title>
    <!--공동 선언 헤더-->
    <? include('subpage/head.html') ?>
    <!--개별 선언 헤더-->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/inquiry.css">
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
</head>


<body>
<div class="wrapper">
    <? include('subpage/navigation.php'); ?>
    <div class="container">
        <? include('subpage/header.php'); ?>
        <div class="content">
            <h1 class="inquiry_title">
                휴가 관리
            </h1>
            <div class="inquiry_wrap">
                <div class="inquiry_con">
                    <div class="inquiry_sort_con f-right">
                        <input type="button" onclick="button1_click_1();" value="월별" />
                        <input type="button" onclick="button1_click_2();" value="년별" />
                        <input type="button" onclick="button1_click_3();" value="유형별" />
                        <input type="button" onclick="button1_click_4();" value="직원별" />
                        <input type="button" onclick="button1_click_5();" value="히스토리" />
                        <? if($idrow['authority']>=2){  ?>
                        <button type="button" onclick="location.href='subpage/table-to-excel.php' ">다운로드</button>
                        <? } ?>
                    </div>
                </div>
                <div class="inquiry_wrap_1" style="display:block;">
                    <? include('subpage/inquiry_1.php');?>
                </div>
                <div class="inquiry_wrap_2" style="display:none;">
                    <? include('subpage/inquiry_2.php');?>
                </div>
                <div class="inquiry_wrap_3" style="display:none; overflow: auto;">
                    <? include('subpage/inquiry_3.php');?>
                </div>
                <div class="inquiry_wrap_4" style="display:none;">
                    <? include('subpage/inquiry_4.php');?>
                </div>     
                <div class="inquiry_wrap_5" style="display:none;">
                    <? include('subpage/inquiry_5.php');?>
                </div> 
            </div>
        </div>
    </div>
</div>
</body>
<script>
    function button1_click_1() {
        jQuery('.inquiry_wrap_1').css("display", "block");    
        jQuery('.inquiry_wrap_2').css("display", "none");    
        jQuery('.inquiry_wrap_3').css("display", "none");
        jQuery('.inquiry_wrap_4').css("display", "none");   
        jQuery('.inquiry_wrap_5').css("display", "none"); 
    }
    function button1_click_2() {
        jQuery('.inquiry_wrap_1').css("display", "none"); 
        jQuery('.inquiry_wrap_2').css("display", "block");    
        jQuery('.inquiry_wrap_3').css("display", "none");
        jQuery('.inquiry_wrap_4').css("display", "none");    
        jQuery('.inquiry_wrap_5').css("display", "none"); 
    }
    function button1_click_3() {
        jQuery('.inquiry_wrap_1').css("display", "none"); 
        jQuery('.inquiry_wrap_2').css("display", "none");    
        jQuery('.inquiry_wrap_3').css("display", "block");  
        jQuery('.inquiry_wrap_4').css("display", "none");      
        jQuery('.inquiry_wrap_5').css("display", "none");    
    }
    function button1_click_4() {
        jQuery('.inquiry_wrap_1').css("display", "none"); 
        jQuery('.inquiry_wrap_2').css("display", "none");    
        jQuery('.inquiry_wrap_3').css("display", "none"); 
        jQuery('.inquiry_wrap_4').css("display", "block");    
        jQuery('.inquiry_wrap_5').css("display", "none");    
        /* history.replaceState({}, null, location.pathname); */
    }
    function button1_click_5() {
        jQuery('.inquiry_wrap_1').css("display", "none"); 
        jQuery('.inquiry_wrap_2').css("display", "none");    
        jQuery('.inquiry_wrap_3').css("display", "none"); 
        jQuery('.inquiry_wrap_4').css("display", "none");   
        jQuery('.inquiry_wrap_5').css("display", "block");     
        /* history.replaceState({}, null, location.pathname); */
    }
</script>
</html>