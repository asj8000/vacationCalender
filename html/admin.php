<?
include('subpage/dbcon.php'); 
include('subpage/check.php');
try { 
    $session_userid=$_SESSION['user_id'];
    $stmt = $con->prepare('select * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
    
} catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$row = $stmt->fetch();  
$userauthority=$row['authority'];

if (is_login()){
    if ($userauthority>=2);
    else{
        header("Location: index.php");
    }
}
else{
    header("Location: welcome.php"); 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>admin - 휴가테이블</title>
    <!--공동 선언 헤더-->
    <? include('subpage/head.html') ?>
    <!--개별 선언 헤더-->
    <script type="text/javascript" src="JS/login_pop_up.js"></script>
    <link rel="stylesheet" href="css/admin.css">
</head>


<body>
<div class="wrapper">
    <? include('subpage/navigation.php'); ?>
    <div class="container">
        <? include('subpage/header.php'); ?>
        <div class="content">
            <div class="row">
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
                            <th>계정 활성 여부</th>
                            <th class="large_witdh">계정 생성시간</th>
                            <th class="small_witdh">수정</th>  
                        </tr>  
                    </thead>
                    <?  
                    $array_team =  array("none","MKT","VOD","tutor","VI","CX","B2B","other");
                    $array_authority = array("member","team leader","admin");
                    $stmt = $con->prepare('SELECT * FROM users ORDER BY uid asc');
                    $stmt->execute();
                    if ($stmt->rowCount() > 0){
                        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                            extract($row);?>
                            <td class="center"><? echo $row['uid'];?></td> 
                            <td><? echo $row['id'];?></td> 
                            <td><? echo $row['username'];?></td>
                            <td><? echo $row['userprofile'];?></td>
                            <td><? echo $array_authority[$row['authority']]; ?></td>
                            <td class="center"><? echo $array_team[$row['team']]; ?></td>
                            <td><? echo $row['startday']; ?></td>
                            <td class="center">
                                <? if($row['activate']) echo "활성"; 
                                else echo "차단됨"; ?>
                            </td>
                            <td><? echo $row['user_regtime']; ?></td>
                            <td class="center"><a class="btn" href="myprofile/admin_editform.php?edit_id=<? echo $row['id'] ?>"><span class="glyphicon glyphicon-pencil"></span> Edit</a></td> 
                            </tr> 
                            <?
                        }
                    }
                    ?>  
                </table>  
            </div>

        </div>
    </div>
</body>
</html>