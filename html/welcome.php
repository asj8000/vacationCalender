<?php
    include('subpage/dbcon.php'); 
    include('subpage/check.php');

    if (is_login()){
        header("Location: index.php");
    }
    else;

    $login_ok = false;
    if ( ($_SERVER['REQUEST_METHOD'] == 'POST') and isset($_POST['login']) ){
        $userid=$_POST['newuserid'];  
        $userpw=$_POST['newuserpw'];  

        if(empty($userid)){
            $errMSG = "아이디를 입력하세요.";
        }
        else if(empty($userpw)){
            $errMSG = "패스워드를 입력하세요.";
        }
        else{
            try{ 
                $stmt = $con->prepare('select * from users where id=:userid');
                $stmt->bindParam(':userid', $userid);
                $stmt->execute();
               
            } 
            catch(PDOException $e) {
                die("Database error." . $e->getMessage()); 
            }

            $row = $stmt->fetch();  
            $salt = $row['salt'];
            $dbuserpw = $row['pw'];
            $activate = $row['activate'];
            
            $decrypted_password = decrypt(base64_decode($dbuserpw), $salt);

            if ( $userpw == $decrypted_password) {
                $login_ok = true;
            }
        }
        if ($login_ok){
            if ($activate==0)
                $errMSG = "허용되지 않은 계정입니다.\n 관리자에게 문의해주세요";
            else if ($activate==2){
                $errMSG = "추가 정보를 입력해주세요";
                echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$errMSG');
                    window.location.replace(\"join_details.php?id=$userid\");
                </SCRIPT>";
            }
            else{
                session_regenerate_id();
                $_SESSION['user_id'] = $userid;
                $_SESSION['ahthority'] = $row['ahthority'];

                if ($row['ahthority']==1)
                    header('location:index.php');
                else{
                    header('location:index.php');
                }
                session_write_close();
            }
        }
        else{
            $errMSG = "아이디나 비밀번호가 맞지 않습니다.";
        }   
    }   
?>


<!DOCTYPE html>
<html>
<head>
	<?php include('subpage/head.html'); ?>
	<title>로그인</title>
	
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="login_wrap on_login_pop_up"></div>
<div class="login_form">
    <h1 align="center" class="">로그인</h1>
    
    <form class="form_login mt-50" method="POST">
        <div class="form form_id">
            <!--<label for="userid">아이디  :</label>-->
            <input type="text" name="newuserid" class="form_control  invalid" id="inputID" placeholder="ID" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" /> 
        </div>
        <div class="form form_pw">
            <!--<label for="user_password"> 패스워드  : </label>-->
            <input type="password" name="newuserpw" class="form_control  invalid" id="inputPW" placeholder="password"required  autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" />
        </div>
        <div class="form pt-60">
            <button class="button_efect" type="submit" name="login">로그인</button>
        </div>
        <div class="form pt-30">
            <a class="" onclick="location.href='/join.php'">
                <span class="" style="cursor:pointer">회원가입 하러가기</span>
            </a>
        </div>
    </form>
</div>
<div class="err_form">
<?php if(isset($errMSG)){ ?>
    <div class="alert alert-danger">
        <!--<span class="glyphicon glyphicon-icon"></span> -->
        <strong><?php echo $errMSG; ?></strong>
    </div>
<?php } else if(isset($successMSG)){    ?>
    <div class="alert alert-success">
        <strong>
            <span class="glyphicon glyphicon-icon"></span> 
            <?php echo $successMSG; ?>
        </strong>
    </div>
<?php } ?> 
</div>
<script>
$(document).ready(function() {
var placeholderTarget = $('.form_id input[type="text"], .form_id input[type="password"]');
});
</script>


</body>
</html>
