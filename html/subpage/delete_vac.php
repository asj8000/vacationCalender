<?php
include('dbcon.php');
include('check.php');

try { 
    $session_userid=$_SESSION['z'];
    $stmt = $con->prepare('SELECT * from users where id=:username');
    $stmt->bindParam(':username', $session_userid);
    $stmt->execute();
    
} catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$idrow = $stmt->fetch(); 

$get_del_vac = $_GET['del_vac'];
if (is_login()){
    if ($idrow['userauthority']>=1);
    else{        
        try { 
            $stmt = $con->prepare('SELECT * from vacation where vacid=:vacid');
            $stmt->bindParam(':vacid', $get_del_vac);
            $stmt->execute();
            $vacrow = $stmt->fetch(); 
        } 
        catch(PDOException $e) {
            die("Database error. " . $e->getMessage()); 
        }
        if($idrow['uid'] != $vacrow['uid']){
            header("location: ../apply.php");
        }
    }
}
else{
    header("Location: welcome.php"); 
}

if(isset($_GET['del_vac'])){
    $time = date("Y-m-d H:i:s");
    $stmt = $con->prepare('UPDATE vacation SET activate = 0, correction_time =:correction_time WHERE vacid =:del_id');
    $stmt->bindParam(':del_id',$_GET['del_vac']);
    $stmt->bindParam(':correction_time',$time);
    $stmt->execute();
}
header("Location: ../apply.php");
?>