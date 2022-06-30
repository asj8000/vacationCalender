<?php
include('subpage/dbcon.php'); 
include('subpage/check.php');
if (is_login()){}
else{
    header("Location: welcome.php");
}

try { 
    $session_userid=$_SESSION['user_id'];
    $stmt = $con->prepare('select * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
   
} catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$row = $stmt->fetch();

$array_profileteam = array("none","마케팅","VOD","Tutor","VI","CX","B2B","other");
$array_authority = array("member","team leader","admin")
?>

<!DOCTYPE html>
<html>
<head>
    <title>내프로필 - 휴가테이블</title>
    <!--공동 선언 헤더-->
    <?php include('subpage/head.html') ?>
    <!--개별 선언 헤더-->
    <link rel="stylesheet" type="text/css" href="/css/myprofile.css">
</head>


<body>

<div class="wrapper">
<?php  
    include('subpage/navigation.php');
?>
    <div class="container">
        <?php include('subpage/header.php'); ?>
        <div class="content">
            <div class="myprofile_page">
                <div class="form">
                    <div class="text f-left">아이디 : </div>
                    <div class="data_out_field f-right">
                        <?php echo $row['id']; ?>
                    </div>
                </div>
                <div class="form">
                    <div class="text f-left">이름 : </div>
                    <div class="data_out_field f-right">
                        <?php echo $row['username']; ?>
                    </div>
                </div>
                <div class="form">
                    <div class="text f-left">닉네임 : </div>
                    <div class="data_out_field f-right">
                        <?php echo $row['userprofile']; ?>
                    </div>
                </div>
                <div class="form">
                    <div class="text f-left">소속 팀 : </div>
                    <div class="data_out_field f-right">
                    <?php echo $array_profileteam[$row['team']]; ?> 팀
                    </div>
                </div>
                <div class="form">
                    <div class="text f-left">입사일 : </div>
                    <div class="data_out_field f-right">
                    <?php echo $row['startday']; ?>
                    </div>
                </div>
                <div class="form">
                    <div class="text f-left">권한 : </div>
                    <div class="data_out_field f-right">
                        <?php echo $array_authority[$row['authority']]; ?>
                    </div>
                </div>

                <div class="edit_profile_button_form">
                    <button class="center edit_profile_button" onclick="location.href='/myprofile/user_detail_editform.php'">정보수정하기</butoon>
                    <button class="center edit_profile_button" onclick="location.href='/myprofile/user_pw_editform.php'">비밀번호변경</butoon>  
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>