<?php
include('subpage/dbcon.php');
include('subpage/check.php');


$get_user_id = $_GET['id'];
try { 
    $stmt = $con->prepare('select * from users where id=:get_user_id');
    $stmt->bindParam(':get_user_id', $get_user_id);
    $stmt->execute();
    
} catch(PDOException $e) {
    die("Database error. " . $e->getMessage()); 
}
$row = $stmt->fetch();  
$activate=$row['activate'];

	
if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])){
	foreach ($_POST as $key => $val){
		if(preg_match('#^__autocomplete_fix_#', $key) === 1){
			$n = substr($key, 19);
			if(isset($_POST[$n])) {
				$_POST[$val] = $_POST[$n];
			}
		}
	}  
	$userstartday=$_POST['newuserstartday'];
	$userteam=$_POST['newuserteam'];
	if(empty($userstartday)){
		$errMSG = "입사일을 등록해주세요";
	}
	else if($userteam == 0){
		$errMSG = "부서를 등록해주세요";
	}
	$useractivate = 0;

	if(!isset($errMSG)){
		try{
			$stmt = $con->prepare('UPDATE users SET activate = :activate, team = :userteam, startday = :userstartday WHERE id=:get_edit_id');
            $stmt->bindParam(':get_edit_id', $_GET['id'] );
            $stmt->bindParam(':userteam', $userteam);
            $stmt->bindParam(':userstartday', $userstartday);
			$stmt->bindparam(':activate',$useractivate);

			if($stmt->execute()){
				$successMSG = "추가정보 기입이 완료되었습니다";
				echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$successMSG');
                    window.location.replace(\"index.php\");
                </SCRIPT>";
			}
			else{
				$errMSG = "사용자 정보 업데이트 오류";
				echo "<SCRIPT type='text/javascript'> //not showing me this
                    alert('$errMSG');
                    window.location.replace(\"join.php\");
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
	<?php include('subpage/head.html'); ?>
	<title>회원가입</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="login_wrap">
	<div class="join_form join_detail">
		<h1 align="center">추가 정보 입력</h1>
		<form id="form" method="post" enctype="multipart/form-data" class="form_login mt-30" >
			<div class="form">
				<td>
					<? $r6 = md5(mt_rand(1, 10000)); ?>
					<select class="f-right select_form form_control invalid" placeholder="부서"  name="newuserteam" value="">
		                <option value="0" selected>부서명</option>
		                <option value="1">MKT</option>
		                <option value="2">VOD</option>
		                <option value="3">tutor</option>
		                <option value="4">VI</option>
		                <option value="5">CX</option>
		                <option value="6">B2B</option>
		            </select>
				</td>
			</div>
			<div class="form day_input">
				<td>
					<? $r7 = md5(mt_rand(1, 10000)); ?>
					<? $nowday = date('Y-m-d');?>
					<label class="ml-100 mt-7_5  center">   입사일 : </label>		
					<input class="f-right none_input_prevent_day form_control invalid" type="date" id="userdate" name="newuserstartday"
		            value="2019-01-01" min="2015-01-01" max="<?php echo $nowday?>" required>
				</td>
			</div>
			<div class="form pt-30">
				<td colspan="2" align="center">
				<button type="submit" name="submit" class="button_efect" >정보입력</button>
				</td>
			</div>
			<div class="form pt-30">
		        <a class="" href="welcome.php">
		            <span class="">로그인 하러가기</span>
		        </a>
		    </div>
		</form>
	</div>
</div>	
<div class="err_form">
<?php if(isset($errMSG)){ ?>
	<div class="alert alert-danger">
		<span class="glyphicon glyphicon-info-sign"></span> 
		<strong><?php echo $errMSG; ?></strong>
	</div>
<?php } else if(isset($successMSG)){	?>
	<div class="alert alert-success">
		<strong><span class="glyphicon glyphicon-info-sign"></span> 
			<?php echo $successMSG; ?>
		</strong>
	</div>
<?php }	?> 
</div>
<script>
function Function() {
  	var x = document.getElementById(".none_input_prevent_day").required;
}
</script>
<script type="text/javascript">
	$('#date').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
</script>
</body>
</html>

