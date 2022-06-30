<?
include('../subpage/dbcon.php'); 
include('../subpage/check.php');
try { 
    $session_userid=$_SESSION['user_id'];
    $stmt = $con->prepare('select * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
    
} 
catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$row = $stmt->fetch();  

if (is_login()){}
else{
    header("Location: ../welcome.php"); 
}


if( ($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['btn_save_updates'])){
    foreach ($_POST as $key => $val){
        if(preg_match('#^__autocomplete_fix_#', $key) === 1){
            $n = substr($key, 19);
            if(isset($_POST[$n])) {
                $_POST[$val] = $_POST[$n];
            }
        }
    }
    $user_name = $_POST['editusername'];
    $user_profile = $_POST['edituserprofile'];
    $user_team = $_POST['edituserteam'];
    $user_startday = $_POST['edituserstartday'];



    if(!isset($user_name)){
        $errMSG = "사원명을 정상적으로 입력하세요";
    }
    else if(empty($user_profile)){
        $errMSG = "닉네임을 정상적으로 입력하세요";
    }
    else if(empty($user_startday)){
        $errMSG = "입사일을 정상적으로 등록해주세요";
    }
    if(!isset($errMSG)){
        try{
            $stmt = $con->prepare('UPDATE users SET username=:username, userprofile=:userprofile, team=:team, startday=:startday WHERE id=:user_id');
            $stmt->bindParam(':user_id',$session_userid);

            $stmt->bindParam(':username',$user_name);
            $stmt->bindParam(':userprofile',$user_profile);
            $stmt->bindParam(':team',$user_team);
            $stmt->bindParam(':startday',$user_startday);
            
            if($stmt->execute()){
                $successMSG = "업데이트가 완료되었습니다";
                echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$successMSG');
                    window.location.replace(\"../myprofile.php\");
                    
                </SCRIPT>";
            }
            else{
                $errMSG = "사용자 추가 에러";
                echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$errMSG');
                    window.location.replace(\"../myprofile.php\");
                </SCRIPT>";
            }
        }
        catch(PDOException $e) {
            die("Database error: " . $e->getMessage()); 
        }
    }           
}
$array_profileteam = array("none","마케팅","VOD","Tutor","VI","CX","B2B","other");
$array_authority = array("member","team leader","admin")
?>

<!DOCTYPE html>
<html>
<head>
    <title>MAKE SOMETHING</title>
    <!--공동 선언 헤더-->
    <?php include('../subpage/head.html') ?>
    <!--개별 선언 헤더-->
    <link rel="stylesheet" href="../css/myprofile.css">
</head>


<body>

<div class="wrapper">
    <?php include('../subpage/navigation.php'); ?>
    <div class="container">
        <?php include('../subpage/header.php'); ?>
        <div class="content">
            <div class="myprofile_page id">
            <h1 class="center"> 프로필 수정하기 </h1>
                <form id="myform" method="post" enctype="multipart/form-data">
                    <div class="form">
                        <div class="text f-left">아이디 : </div>
                        <div class="data_out_field f-right">
                            <?php echo $row['id']; ?>
                        </div>
                    </div>
                    <div class="form">
                        <div class="text f-left">이름 : </div>
                        <? $r1 = md5(mt_rand(1, 10000)); ?>
                        <input class="data_out_field f-right" type="text" name="<? echo $r1; ?>" value="<?php echo $row['username']; ?>" placeholder="이름을 입력하세요." 
                               autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required />
                        <input type="hidden" name="__autocomplete_fix_<? echo $r1; ?>" value="editusername" /> 
                    </div>
                    <div class="form">
                        <div class="text f-left">닉네임 : </div>
                        <? $r2 = md5(mt_rand(1, 10000)); ?>
                        <input class="data_out_field f-right" type="text" name="<? echo $r2; ?>" value="<?php echo $row['userprofile']; ?>" placeholder="프로필을 입력하세요." 
                               autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required />
                        <input type="hidden" name="__autocomplete_fix_<? echo $r2; ?>" value="edituserprofile" /> 
                    </div>
                    <div class="form">
                        <div class="text f-left">소속 팀 : </div>
                        <select class="data_out_field f-right" placeholder="부서" name="edituserteam" value="">
                            <option value="1" <? if ($row['team']==1){echo "selected";} ?>>MKT</option>
                            <option value="2" <? if ($row['team']==2){echo "selected";} ?>>VOD</option>
                            <option value="3" <? if ($row['team']==3){echo "selected";} ?>>tutor</option>
                            <option value="4" <? if ($row['team']==4){echo "selected";} ?>>VI</option>
                            <option value="5" <? if ($row['team']==5){echo "selected";} ?>>CX</option>
                            <option value="6" <? if ($row['team']==6){echo "selected";} ?>>B2B</option>
                            <?if($row['team']==7){?>
                                <option value="7" <? if ($row['team']==7){echo "selected";} ?>>other</option>
                            <?}?>
                        </select>
                    </div>
                    <div class="form">
                        <div class="text f-left">입사일 : </div>
                        <input class="data_out_field f-right" type="date" id="userdate" name="edituserstartday"
                        value="<? echo $row['startday'];?>" min="2015-01-01" max="<?php echo $nowday?>" required>

                    </div>
                    <div class="form">
                        <div class="text f-left">권한 : </div>
                        <div class="data_out_field f-right">
                            <?php echo $array_authority[$row['authority']]; ?>
                        </div>
                    </div>

                    <div class="edit_profile_button_form">

                        <button type="submit" name="btn_save_updates" class="center edit_profile_button" ><span class="glyphicon glyphicon-floppy-save"></span>업데이트</button>
                        <span><a class="btn btn-warning" href="../myprofile.php"> <span class="glyphicon glyphicon-remove"></span>&nbsp; 취소</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

