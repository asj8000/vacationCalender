<?php
include('subpage/dbcon.php');    
include('subpage/check.php');

if (is_login()){

    unset($_SESSION['user_id']);
    session_destroy();
}

header("Location: index.php");
?>