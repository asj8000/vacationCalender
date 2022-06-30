<?php
    include('subpage/dbcon.php'); 
    include('subpage/check.php');

?>
<?php 
    if (is_login()){}
    else{header("Location: welcome.php"); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>메인 - 휴가테이블 </title>
    <!--공동 선언 헤더-->
    <?php include('subpage/head.html') ?>
    <!--개별 선언 헤더-->
    <script type="text/javascript" src="JS/login_pop_up.js"></script>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/calendar.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>


<body>

<div class="wrapper">
<?php  
    include('subpage/navigation.php');
?>
    <div class="container">
        <?php include('subpage/header.php'); ?>
        <div class="content">
            <?php include('subpage/calendar.php');?>
        </div>
    </div>
</div>

</body>
</html>


