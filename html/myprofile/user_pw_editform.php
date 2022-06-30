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
    $nowpassword = $_POST['nowpassword'];
    $user_newpassword = $_POST['user_newpassword'];
    $user_newpassword_re = $_POST['user_newpassword_re'];


    if(empty($nowpassword)){
        $errMSG = "패스워드를 입력하세요.";
    }
    else if(empty($user_newpassword)){
        $errMSG = "새 패스워드를 입력하세요.";
    }
    else if(empty($user_newpassword)){
        $errMSG = "새 패스워드 확인을 입력하세요.";
    }     


    $salt = $row['salt'];
    $dbuserpw = $row['pw'];
    
    $decrypted_password = decrypt(base64_decode($dbuserpw), $salt);
 
    if($nowpassword != $decrypted_password){
        $errMSG = "현재 비밀번호가 일치하지 않습니다.";
    }
    else if( $user_newpassword != $user_newpassword_re ){
        $errMSG = "새 비밀번호가 일치하지 않습니다.";
    }

    if(!isset($errMSG)){
        try{
            $stmt = $con->prepare('UPDATE users SET pw=:password WHERE id=:user_id');
            $stmt->bindParam(':user_id',$session_userid);
            $encrypted_password = base64_encode(encrypt($user_newpassword, $salt));
            $stmt->bindParam(':password',$encrypted_password);
            
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
            <div class="myprofile_page pw">
                <form id="myform" method="post" enctype="multipart/form-data">    
                    <h1 class="center">비밀번호 변경 </h1>
                    <div class="form">
                        <div class="text f-left">현재 비밀번호 : </div>
                        <? $r1 = md5(mt_rand(1, 10000)); ?>
                        <input class="data_out_field f-right" type="password" name="<? echo $r1; ?>" placeholder="현재 비밀번호" 
                               autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required />
                        <input type="hidden" name="__autocomplete_fix_<? echo $r1; ?>" value="nowpassword" /> 
                    </div>
                    <div class="form">
                        <div class="text f-left">새 비밀번호 : </div>
                        <? $r2 = md5(mt_rand(1, 10000)); ?>
                        <input class="data_out_field f-right" min="8" type="password" name="<? echo $r2; ?>" placeholder="새 비밀번호" 
                               autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required />
                        <input type="hidden" name="__autocomplete_fix_<? echo $r2; ?>" value="user_newpassword" /> 
                    </div>
                    <div class="form">
                        <div class="text f-left">새 비밀번호 확인 : </div>
                        <? $r3 = md5(mt_rand(1, 10000)); ?>
                        <input class="data_out_field f-right" type="password" name="<? echo $r3; ?>" placeholder="새 비밀번호 확인" 
                               autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required />
                        <input type="hidden" name="__autocomplete_fix_<? echo $r3; ?>" value="user_newpassword_re" /> 
                    </div>
                    
                    <div class="edit_profile_button_form">

                        <button type="submit" name="btn_save_updates" class="center edit_profile_button" ><span class="glyphicon glyphicon-floppy-save"></span>업데이트</button>
                        <span><a class="btn btn-warning" href="../myprofile.php"> <span class="glyphicon glyphicon-remove"></span>&nbsp; 취소</a></span>
                    </div>
                </form>
            </div>
            <div class="err_form">
            <?php if(isset($errMSG)){ ?>
                <div class="alert alert-danger">
                    <span class="glyphicon glyphicon-info-sign"></span> 
                    <strong><?php echo $errMSG; ?></strong>
                </div>
            <?php } else if(isset($successMSG)){    ?>
                <div class="alert alert-success">
                    <strong><span class="glyphicon glyphicon-info-sign"></span> 
                        <?php echo $successMSG; ?>
                    </strong>
                </div>
            <?php } ?> 
            </div>
        </div>
    </div>
</div>
</body>
</html>

