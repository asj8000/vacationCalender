<?php /*if ($_SESSION['user_id'] == 'admin' && $_SESSION['is_admin']==1){ ?>
            <button class="button" onclick="location.href='admin.php'">어드민모드</button>
        <?php } ?>
        <?php if (isset($_SESSION['user_id'])) { ?>
        <button class="button" onclick="location.href='logout.php'"> 로그아웃</a></button>
        <li><a href="editprofile.php"> <?php echo $_SESSION['user_id']; ?>님 하위 </a></li>

        <?php } else { ?>
            <button class="button on_login_pop_up"> 로그인 </button>
        <?php } ?>
        </ul>
        <button class="button f-right" onclick="location.href='logout.php'"> 로그아웃</button>
*/ ?>

<div class="header">
    <?php
        try { 
            $session_userid=$_SESSION['user_id'];
            $stmt = $con->prepare('select * from users where id=:username');
            $stmt->bindParam(':username', $session_userid);
            $stmt->execute();
           
        } catch(PDOException $e) {
            die("Database error. " . $e->getMessage()); 
        }
        $row = $stmt->fetch();  
        $headeruserprofile=$row['userprofile'];


        $request_uri = $_SERVER["PHP_SELF"];
        if ($request_uri=="/" or $request_uri=="/index.php") {
            $page_header_MSG = "휴가 캘린더";
        }
        else if ($request_uri=="/apply.php") {
            $page_header_MSG = "휴가 신청";
        }
        else if ($request_uri=="/admin.php") {
            $page_header_MSG = "관리자 페이지";
        }
        else if ($request_uri=="/FAQ.php") {
            $page_header_MSG = "FAQ";
        }
        else if ($request_uri=="/inquiry.php") {
            $page_header_MSG = "휴가 조회";
        }
        else {
            $page_header_MSG = "프로필 수정";
        }
        $now_page_number='0';
        $request_uri = $_SERVER["PHP_SELF"];

        if ($request_uri=="/" or $request_uri=="/index.php") {
            $now_page_number='1';
        }
        else if ($request_uri=="/apply.php") {
            $now_page_number='2';
        }
        else if ($request_uri=="/FAQ.php") {
            $now_page_number='3';
        }
        else if ($request_uri=="/myprofile.php"){
            $now_page_number='4';
        }
        else{
            $now_page_number='4';
        }
    ?>

    <h1 class="logo f-left" onclick="location.href='#'">
        <span><?php echo $page_header_MSG ?></span>
    </h1>
    <div class="header_content f-right">
        <div class="header_info f-right">
            <button class="button_1 f-right" onclick="location.href='/logout.php'"> 로그아웃</button>
            <div class="profile f-right"><a href="/myprofile.php"><?php echo $headeruserprofile; ?>님</a></div>
        </div>
    
    
    </div>
    
    <div class="hamburger" id="sidebar_button">
        <div class="hamburger_bar"></div>
        <div class="hamburger_bar"></div>
        <div class="hamburger_bar"></div>
    </div>
        <div class="hamburger_background close_sidebar" style="display: none;"></div>
    <div class="hamburger_wrap" id="sidebar">
        <div class="hamburger_nav" style="right: -200px;">
            <div class="form">
                <button type="button" class="index_page_button _index" onclick="location.href='/index.php'"><img src="/images/calander.png" width="150px" height="150px" class=" <?php if($now_page_number==1) echo 'on' ?>" /></button>
            </div>
            <div class="form">
                <button type="button" class="index_page_button _index" onclick="location.href='/apply.php'"><img src="/images/apply.png" width="150px" height="150px" class=" <?php if($now_page_number==2) echo 'on' ?>" /></button>
            </div>

            <div class="form">
                <button type="button" class="index_page_button _index" onclick="location.href='/FAQ.php'"><img src="/images/question.png" width="150px" height="150px" class=" <?php if($now_page_number==3) echo 'on' ?>" /></button>
            </div>
            <div class="form button_0">
                <button type="button" class="index_page_button _index" onclick="location.href='/myprofile.php'"><img src="/images/editprofile.png" width="150px" height="150px" class=" <?php if($now_page_number==4) echo 'on' ?>" /></button>
            </div>
            <div class="form button_1">
                <button type="button" class="index_page_button _index" onclick="location.href='/logout.php'"><img src="/images/logout.png" width="130px" height="130px"/></button>
            </div>
        </div>

    </div>
</div>

<script>
$(function(){
    $("#sidebar_button").click(function(){
        $('#sidebar').stop(true).animate({right:'0px'}, 300);
        $('.hamburger_background').toggle();
    });
    $(".close_sidebar").click(function(){
        $('#sidebar').toggleClass('open');
        $('#sidebar').stop(true).animate({right:'-200px'}, 300);
        $('.hamburger_background').toggle();
    });
});
</script>