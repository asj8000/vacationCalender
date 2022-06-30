<?
include('../subpage/dbcon.php'); 
include('../subpage/check.php');
try { 
    $session_userid=$_SESSION['user_id'];
    $stmt = $con->prepare('select * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
    
} catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$row = $stmt->fetch();  

if (is_login()){
    if ($row['authority']>=2);
    else{
        header("Location: ../index.php");
    }
}
else{
    header("Location: ../welcome.php"); 
}

if(isset($_GET['edit_id']) && !empty($_GET['edit_id'])){
    $edit_id = $_GET['edit_id'];
    $stmt_edit = $con->prepare('SELECT * FROM users WHERE id = :user_id');
    $stmt_edit->execute(array(':user_id'=>$edit_id));
    $edit_row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
    extract($edit_row);
}
else{
    header("Location: admin.php");
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
    if ( isset($_POST['activate'])) $activate=1;
    else $activate=0;
    $user_name = $_POST['editusername'];
    $user_profile = $_POST['edituserprofile'];
    $user_authority = $_POST['edituserauthority'];
    $user_team = $_POST['edituserteam'];
    $user_startday = $_POST['edituserstartday'];


    
    if(isset($_POST['reset_pw'])){ //비밀번호 초기화 눌렀을 때
        $default_password = '1234';
        $salt = $edit_row['salt']; 
        $encrypted_password = base64_encode(encrypt($default_password, $salt));
    }
    else{
        $encrypted_password = $edit_row['pw'];
    }

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
            $stmt = $con->prepare('UPDATE users SET pw=:pw, username=:username, userprofile=:userprofile, authority=:authority, team=:team, startday=:startday, activate=:activate WHERE id=:user_id');
            $stmt->bindParam(':user_id',$edit_id);

            $stmt->bindParam(':pw',$encrypted_password);
            $stmt->bindParam(':username',$user_name);
            $stmt->bindParam(':userprofile',$user_profile);
            $stmt->bindParam(':authority',$user_authority);
            $stmt->bindParam(':team',$user_team);
            $stmt->bindParam(':startday',$user_startday);
            $stmt->bindParam(':activate',$activate);
            
            if($stmt->execute()){
                $successMSG = "업데이트가 완료되었습니다";
                echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$successMSG');
                    window.location.replace(\"../admin.php\");
                    
                </SCRIPT>";
            }
            else{
                $errMSG = "사용자 추가 에러";
                echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$errMSG');
                    window.location.replace(\"../admin.php\");
                </SCRIPT>";
            }
        }
        catch(PDOException $e) {
            die("Database error: " . $e->getMessage()); 
        }
    }           
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>admin - 휴가테이블</title>
    <!--공동 선언 헤더-->
    <? include('../subpage/head.html') ?>
    <!--개별 선언 헤더-->
    <script type="text/javascript" src="JS/login_pop_up.js"></script>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/apply.css">
    <style>
        .content .form_control{ width: 100px; }
    </style>
</head>


<body>
<div class="wrapper">
    <? include('../subpage/navigation.php'); ?>
    <div class="container">
        <? include('../subpage/header.php'); ?>                    
        <div class="content">
            <h1 class="h2">&nbsp; 사용자 정보 수정</h1>
            <form id="edit_form" method="post" enctype="multipart/form-data" class="form-horizontal">
                <table style="table-layout: fixed">  
                    <thead>  
                        <tr>  
                            <th class="small_witdh">UID </th>
                            <th class="f-left">아이디</th>  
                            <th>사원명</th>
                            <th>닉네임</th>
                            <th class="small_witdh">권한</th>
                            <th class="small_witdh">팀</th>
                            <th>입사 날짜</th>
                            <th style="width: 150px;">비밀번호 초기화하기</th>
                            <th>계정 활성화</th>
                            <th class="large_witdh">계정 생성시간</th>
                        </tr>  
                    </thead>
                    <?if(isset($errMSG)){?>
                        <div class="alert alert-danger">
                            <span class="glyphicon glyphicon-info-sign"></span> &nbsp; <? echo $errMSG; ?>
                        </div>
                    <?}?>
                    <tbody>
                        <td class="center"><? echo $edit_row['uid'];?></td> 
                        <td><? echo $edit_row['id'];?></td> 
                        <td>
                            <? $r1 = md5(mt_rand(1, 10000)); ?>
                            <input class="f-left form_control" type="text" value="<? echo $edit_row['username']?>" name="<? echo $r1; ?>" placeholder="<? echo $edit_row['username']?>" autocomplete="off" minlength="2" maxlength="24" readonly onfocus="this.removeAttribute('readonly');" required />
                            <input type="hidden" name="__autocomplete_fix_<? echo $r1; ?>" value="editusername" /> 
                        </td>
                        <td>
                            <? $r2 = md5(mt_rand(1, 10000)); ?>
                            <input class="form-control" type="text" name="<? echo $r2; ?>" value="<?php echo $edit_row['userprofile']; ?>" placeholder="프로필을 입력하세요." 
                                   autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required />
                            <input type="hidden" name="__autocomplete_fix_<? echo $r2; ?>" value="edituserprofile" /> 
                        </td>
                        <td>
                            <select class="f-right form_control" placeholder="부서" name="edituserauthority" value="">
                                <option value="0" <? if ($edit_row['authority']==0){echo "selected";} ?>>user</option>
                                <option value="1" <? if ($edit_row['authority']==1){echo "selected";} ?>>teamlader</option>
                                <option value="2" <? if ($edit_row['authority']==2){echo "selected";} ?>>admin</option>
                            </select>
                        </td>
                        <td>
                            <select class="f-right form_control" placeholder="부서" name="edituserteam" value="">
                                <option value="0" <? if ($edit_row['team']==0){echo "selected";} ?>>none</option>
                                <option value="1" <? if ($edit_row['team']==1){echo "selected";} ?>>MKT</option>
                                <option value="2" <? if ($edit_row['team']==2){echo "selected";} ?>>VOD</option>
                                <option value="3" <? if ($edit_row['team']==3){echo "selected";} ?>>tutor</option>
                                <option value="4" <? if ($edit_row['team']==4){echo "selected";} ?>>VI</option>
                                <option value="5" <? if ($edit_row['team']==5){echo "selected";} ?>>CX</option>
                                <option value="6" <? if ($edit_row['team']==6){echo "selected";} ?>>B2B</option>
                                <option value="7" <? if ($edit_row['team']==7){echo "selected";} ?>>other</option>
                            </select>
                        </td>
                        <td>
                            <? $r5 = md5(mt_rand(1, 10000)); ?>
                            <? $nowday = date('Y-m-d');?>  
                            <input style="width: 120px" class="f-right form_control" type="date" id="userdate" name="edituserstartday"
                            value="<? echo $edit_row['startday'];?>" min="2015-01-01" max="<?php echo $nowday?>" required>
                        </td>
                        <td>
                            <input type="checkbox" id="c1" name="reset_pw" />
                            <label name="reset_pw" for="c1"><span></span> 비번 1234로 변경</label>
                        </td>
                        <td>
                            <?php if($activate){ ?>
                                <input type="checkbox" name="activate" checked >     
                            <?php } else{ ?>
                                <input type="checkbox" name="activate" >     
                            <?php } ?>
                        </td>
                        <td style="width: 180px;" ><? echo $row['user_regtime']; ?></td>
                    </tbody>
                </table>  
                <div class="dete_insert_check_box_form">
                    <button type="submit" name="btn_save_updates" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span>업데이트</button>
                    <a class="btn btn-warning" href="../admin.php"> <span class="glyphicon glyphicon-remove"></span>&nbsp; 취소</a>
                </div>
            </form>  
        </div>
    </div>
</div>
</body>
</html>