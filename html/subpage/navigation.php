
    
<?php
    $now_page_number='0';
    $request_uri = $_SERVER["PHP_SELF"];

    if ($request_uri=="/" or $request_uri=="/index.php") {
        $now_page_number='1';
    }
    else if ($request_uri=="/apply.php") {
        $now_page_number='2';
    }
    else if ($request_uri=="/inquiry.php") {
        $now_page_number='3';
    }
    else if ($request_uri=="/myprofile.php") {
        $now_page_number='4';
    }
    else if ($request_uri=="/admin.php"){
        $now_page_number='5';
    }
    else if ($request_uri=="/FAQ.php"){
        $now_page_number='6';
    }
    else{
        $now_page_number='4';
    }

?>
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
    $navuserauthority=$row['authority'];
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<div class="navigation">

	<div class="form">
		<button type="button" class="index_page_button _index <?php if($now_page_number==1) echo 'on' ?>" onclick="location.href='/index.php'"></button>
	</div>
	<div class="form">
		<button type="button" class="apply_page_button <?php if($now_page_number==2) echo 'on' ?>" onclick="location.href='/apply.php' "></button>
	</div>
    <?php if($navuserauthority>=1){ ?>
    	<div class="form">
    		<button type="button" class="inquiry_page_button <?php if($now_page_number==3) echo 'on' ?>" onclick="location.href='/inquiry.php'"></button>
    	</div>
    <?php } ?>
    <?php if($navuserauthority>=2){ ?>
        <div class="form">
            <button type="button" class="admin_page_button <?php if($now_page_number==5) echo 'on' ?>" onclick="location.href='/admin.php'"></button>
        </div>
    <?php } ?>
	<div class="form bottom_0">
		<button type="button" class="editprofile_page_button <?php if($now_page_number==4) echo 'on' ?>" onclick="location.href='/myprofile.php' "></button>
	</div>
    <div class="form bottom_1">
        <button type="button" class="question_page_button <?php if($now_page_number==6) echo 'on' ?>" onclick="location.href='/FAQ.php' "></button>
    </div>
</div>
