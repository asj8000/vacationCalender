<?php
include('subpage/dbcon.php');
include('subpage/check.php');
if (is_login()){
	header("Location: index.php");
}
else;

function validatePassword($password){
	//Begin basic testing
	if(strlen($password) < 8 || empty($password)) {
		return 0;
	}
	if((strlen($password) > 48)) {
		return 0;
	}	
	if(preg_match('/[A-Z]/',$password) == (0 || false)){
		return 1;
	}
	if(!preg_match('/[\d]/',$password) != (0 || false)){
		return 2;
	}
	if(preg_match('/[\W]/',$password) == (0 || false)){
		return 3;
	}
	return true;
}

	
if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])){
	foreach ($_POST as $key => $val){
		if(preg_match('#^__autocomplete_fix_#', $key) === 1){
			$n = substr($key, 19);
			if(isset($_POST[$n])) {
				$_POST[$val] = $_POST[$n];
			}
		}
	}  

	$userid=$_POST['newuserid'];
	$password=$_POST['newpassword'];
	$username=$_POST['newusername'];
	$userprofile=$_POST['newuserprofile'];
	if (!validatePassword($password)){
     	$errMSG = "패스워드를 8자이상 입력해주세요";
	}

	if(empty($userid)){
		$errMSG = "아이디를 입력하세요";
	}
	else if(empty($password)){
		$errMSG = "패스워드를 입력하세요";
	}
	else if ($_POST['newpassword'] != $_POST['newconfirmpassword']) {
		$errMSG = "패스워드가 일치하지 않습니다.";
	}
	else if(empty($username)){
		$errMSG = "성함을 입력하세요";
	}
	else if(empty($userprofile)){
		$errMSG = "닉네임을 입력하세요";
	} 
	try { 
		$stmt = $con->prepare('select * from users where id=:userid');
		$stmt->bindParam(':userid', $userid);
		$stmt->execute();
    } 
    catch(PDOException $e) {
		die("Database error: " . $e->getMessage()); 
    }
    $row = $stmt->fetch();
    if ($row){
		$errMSG = "이미 존재하는 아이디입니다.";
    }
	$user_uid = 0; //defalt 값이 수정되지 않으면 현재 uid (37초과) 최고값 + 1의 uid를 지정
	$userauthority = 0; //defalt 권한
	$get_more_information = 0; //defalt 입력되지 않은 정보의 경우 더 받아옴
	$userteam = 0; //defalt
	$userstartday = '1001-01-01'; //defalt
	$useractivate = 1;
    switch ($username) {
       case '김윤환': $user_uid =  1; $userteam=7; $userauthority = 1; $userstartday = '2015-05-01'; break;
       case '김영경': $user_uid =  2; $userteam=4; $userauthority = 2; $userstartday = '2015-05-01'; break;
       case '정태호': $user_uid =  3; $userteam=4; $userauthority = 1; $userstartday = '2016-05-01'; break;
       case '장승린': $user_uid =  4; $userteam=2; $userauthority = 1; $userstartday = '2016-05-01'; break;
       case '김익정': $user_uid =  5; $userteam=3; $userauthority = 1; $userstartday = '2016-05-01'; break;
       case '주재학': $user_uid =  6; $userteam=6; $userauthority = 1; $userstartday = '2017-03-20'; break;
       case '이선영': $user_uid =  7; $userteam=4; $userauthority = 0; $userstartday = '2017-08-01'; break;
       case '최아름': $user_uid =  8; $userteam=3; $userauthority = 0; $userstartday = '2018-02-01'; break;
       case '박혜린': $user_uid =  9; $userteam=1; $userauthority = 0; $userstartday = '2018-04-01'; break;
       case '김지훈': $user_uid = 10; $userteam=3; $userauthority = 0; $userstartday = '2018-06-25'; break;
       case '박서현': $user_uid = 11; $userteam=1; $userauthority = 0; $userstartday = '2018-07-16'; break;
       case '이혜민': $user_uid = 12; $userteam=4; $userauthority = 0; $userstartday = '2018-08-01'; break;
       case '김소진': $user_uid = 13; $userteam=3; $userauthority = 0; $userstartday = '2018-10-16'; break;
       case '김예지': $user_uid = 14; $userteam=1; $userauthority = 0; $userstartday = '2019-01-01'; break;
       case '이수빈': $user_uid = 15; $userteam=5; $userauthority = 1; $userstartday = '2019-02-12'; break;
       case '김소희': $user_uid = 16; $userteam=1; $userauthority = 0; $userstartday = '2019-02-07'; break;
       case '이승한': $user_uid = 17; $userteam=4; $userauthority = 0; $userstartday = '2019-02-26'; break;
       case '윤채훈': $user_uid = 18; $userteam=3; $userauthority = 0; $userstartday = '2019-02-26'; break;
       case '박수정': $user_uid = 19; $userteam=1; $userauthority = 1; $userstartday = '2019-05-01'; break;
       case '이해윤': $user_uid = 20; $userteam=1; $userauthority = 0; $userstartday = '2019-05-01'; break;
       case '최자민': $user_uid = 21; $userteam=2; $userauthority = 0; $userstartday = '2019-06-01'; break;
       case '강태호': $user_uid = 22; $userteam=4; $userauthority = 0; $userstartday = '2019-07-01'; break;
       case '정재범': $user_uid = 23; $userteam=3; $userauthority = 0; $userstartday = '2019-07-01'; break;
       case '김다희': $user_uid = 24; $userteam=3; $userauthority = 0; $userstartday = '2019-07-01'; break;
       case '조유리': $user_uid = 25; $userteam=2; $userauthority = 0; $userstartday = '2019-07-01'; break;
       case '김수연': $user_uid = 26; $userteam=3; $userauthority = 0; $userstartday = '2019-08-01'; break;
       case '조민희': $user_uid = 27; $userteam=5; $userauthority = 0; $userstartday = '2019-08-12'; break;
       case '안성제': $user_uid = 28; $userteam=4; $userauthority = 0; $userstartday = '2019-09-02'; break;
       case '김송이': $user_uid = 29; $userteam=7; $userauthority = 2; $userstartday = '2019-10-01'; break;
       case '최강식': $user_uid = 30; $userteam=4; $userauthority = 0; $useractivate = 2; break;
       case '김민숙': $user_uid = 31; $userteam=4; $userauthority = 0; $useractivate = 2; break;
       case '조유정': $user_uid = 32; $userteam=1; $userauthority = 0; $userstartday = '2019-10-07'; break;
       case '김선영': $user_uid = 33; $userteam=2; $userauthority = 0; $userstartday = '2019-10-10'; break;
       case '한나라': $user_uid = 34; $userteam=2; $userauthority = 0; $userstartday = '2019-11-01'; break;
       case '이단비': $user_uid = 35; $userteam=4; $userauthority = 0; $userstartday = '2019-11-04'; break;
       case '이수진': $user_uid = 36; $userteam=6; $userauthority = 0; $userstartday = '2019-11-13'; break;
       case '황선수': $user_uid = 37; $userteam=4; $userauthority = 0; $userstartday = '2019-12-02'; break;
       default:
          $useractivate = 2;
          break;
    }
    try { 
		$stmt = $con->prepare('select * from users where uid=:user_uid');
		$stmt->bindParam(':user_uid', $user_uid);
		$stmt->execute();
    } 
    catch(PDOException $e) {
		die("Database error: " . $e->getMessage()); 
    }
    $row = $stmt->fetch();
    if ($row){
		$errMSG = "이미 존재하는 이름입니다.";
    }
/*
1 	대표	김윤환 2015-05-01
2 	VI ( 개발 )	김영경 2015-05-01
3 	VI ( 개발 )	정태호 2016-05-01
4 	VOD	장승린 2016-05-01
5 	카테고리매니저	김익정 2016-05-01
6 	B2B	주재학  2017-03-20
7 	VI ( 개발 )	이선영 2017-08-01
8 	카테고리매니저	최아름 2018-02-01
9 	마케팅	박혜린 2018-04-01
10 	카테고리매니저	김지훈 2018-06-25
11	마케팅	박서현 2018-07-16
12 	VI (CS기획)	이혜민 2018-08-01
13 	카테고리매니저	김소진 2018-10-16
14 	마케팅	김예지 2019-01-01
15 	15CS	이수빈 2019-02-12
16 	마케팅	김소희 2019-02-07
17 	VI ( 개발 )	이승한 2019-02-26
18 	카테고리매니저	윤채훈 2019-02-26
19 	마케팅	박수정 2019-05-01
20 	마케팅	이해윤 2019-05-01
21 	VOD	최자민 2019-06-01
22 	VI ( 개발 )	강태호 2019-07-01
23 	카테고리매니저	정재범 2019-07-01
24	카테고리매니저	김다희 2019-07-01
25 	VOD	조유리 2019-07-01
26 	카테고리매니저	김수연 2019-08-01
27 	CS	조민희 2019-08-12
28 	VI ( 개발 )	안성제 2019-09-02
29 	경영지원	김송이 2019-10-01
30  VI ( 개발 ) 최강식
31  VI ( 디자인 ) 김민숙

32 	VOD	김선영 2019-10-10
33 	마케팅	조유정 2019-10-07
34 	VOD 한나라 2019-11-01
35  VI ( 개발 ) 이단비 2019-11-04
36  B2B 이수진 
37  VI ( 개발 ) 황선수 2019-12-02
*/ 
    try { 
	    $stmt = $con->prepare('select MAX(uid) from users');
	    $stmt->execute();
	} 
	catch(PDOException $e) {
	    die("Database error: " . $e->getMessage()); 
	} 
	$uid_max_check = $stmt->fetch();
   if($user_uid==0){
   		if(max($uid_max_check)>=38){
	        $user_uid = max($uid_max_check) + 1;
	    }
	    else{
	    	$user_uid = 38;
	    }
   }
	if(!isset($errMSG)){
		try{
			$stmt = $con->prepare('INSERT INTO users(uid, id, pw, salt, username, userprofile, authority, team, startday, activate ) VALUES(:uid, :userid, :password, :salt, :username, :userprofile, :authority, :team, :startday, :activate )');
			$stmt->bindParam(':uid',$user_uid);
			$stmt->bindParam(':userid',$userid);
			$salt = bin2hex(openssl_random_pseudo_bytes(32));
			$encrypted_password = base64_encode(encrypt($password, $salt));
			$stmt->bindParam(':password', $encrypted_password);
			$stmt->bindParam(':username',$username);
			$stmt->bindParam(':userprofile',$userprofile);
			$stmt->bindParam(':salt',$salt); 
			$stmt->bindParam(':authority',$userauthority);
			$stmt->bindParam(':team',$userteam);
			$stmt->bindparam(':startday',$userstartday);
			$stmt->bindparam(':activate',$useractivate);
			if($stmt->execute()){
				if($useractivate==1){
					$successMSG = "회원가입이 완료되었습니다";
					echo "<SCRIPT type='text/javascript'> //not showing me this
	                    alert('$successMSG');
	                    window.location.replace(\"index.php\");
	                </SCRIPT>";
	            }
	            else if($useractivate==2){
	            	$successMSG = "추가 정보를 입력해주세요";
					echo "<SCRIPT type='text/javascript'> //not showing me this
	                    alert('$successMSG');
	                    window.location.replace(\"join_details.php?id=$userid\");
	                </SCRIPT>";
	            }
	            else if($useractivate==0){
	            	$successMSG = "허용되지 않은 계정입니다.\n관리자에게 문의하세요";
					echo "<SCRIPT type='text/javascript'> //not showing me this
	                    alert('$successMSG');
	                    window.location.replace(\"join_details.php?id=$userid\");
	                </SCRIPT>";
	            }
	            else{
	            	$successMSG = "오류 발생";
					echo "<SCRIPT type='text/javascript'> //not showing me this
	                    alert('$successMSG');
	                    window.location.replace(\"welcome.php\");
	                </SCRIPT>";
	            }
			}
			else{
				$errMSG = "사용자 추가 에러";
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
	<div class="join_form">
		<h1 align="center">회원가입</h1>
		<form id="form" method="post" enctype="multipart/form-data" class="form_login mt-30" >
			<div class="form">
				<? $r1 = md5(mt_rand(1, 10000)); ?>
				<td>
					<input class="f-left none_input_prevent_id full_form form_control invalid" type="text" name="<? echo $r1; ?>" placeholder="아이디를 입력해주세요" autocomplete="off" minlength="2" maxlength="24" readonly onfocus="this.removeAttribute('readonly');" required />
					<input type="hidden" name="__autocomplete_fix_<? echo $r1; ?>" value="newuserid" /> 
				</td>
			</div>
			<div class="form mt-50">
				<? $r2 = md5(mt_rand(1, 10000)); ?>
				<td>
					<input class="f-left none_input_prevent_pw form_control invalid" type="password" minlength="8" maxlength="48" name="<? echo $r2; ?>"  placeholder="패스워드 (8자 이상)" autocomplete="off" readonly 
						   onfocus="this.removeAttribute('readonly');" required />
					<input type="hidden" name="__autocomplete_fix_<? echo $r2; ?>" value="newpassword" />
				</td>
				<? $r3 = md5(mt_rand(1, 10000)); ?>
				<td>
					<input class="f-right none_input_prevent_rpw form_control invalid" type="password" minlength="8" maxlength="48" name="<? echo $r3; ?>"  placeholder="패스워드 재확인" autocomplete="off" readonly 
						   onfocus="this.removeAttribute('readonly');" required />
					<input type="hidden" name="__autocomplete_fix_<? echo $r3; ?>" value="newconfirmpassword" /> 
				</td>
			</div>
			<div class="form">
				<? $r4 = md5(mt_rand(1, 10000)); ?>
				<td>
					<input class="full_form f-left none_input_prevent_profile form_control invalid" type="text" min="1" maxlength="48" name="<? echo $r4; ?>" placeholder="성함을 입력해주세요" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required />
					<input type="hidden" name="__autocomplete_fix_<? echo $r4; ?>" value="newusername" /> 
				</td>	
			</div> 
			<div class="form">
				<? $r5 = md5(mt_rand(1, 10000)); ?>
				<td>
					<input class="full_form f-right none_input_prevent_profile form_control invalid" type="text" min="1" maxlength="48" name="<? echo $r5; ?>" placeholder="닉네임을 입력해주세요" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" required />
					<input type="hidden" name="__autocomplete_fix_<? echo $r5; ?>" value="newuserprofile" /> 
				</td>
			</div>
			<div class="form pt-30">
				<td colspan="2" align="center">
				<button type="submit" name="submit" class="button_efect" >회원가입</button>
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
  	var x = document.getElementById(".none_input_prevent_id").required;
  	var x = document.getElementById(".none_input_prevent_pw").required;
  	var x = document.getElementById(".none_input_prevent_rpw").required;
  	var x = document.getElementById(".none_input_prevent_profile").required;
  	var x = document.getElementById(".none_input_prevent_day").required;
}
</script>
<script type="text/javascript">
	$('#date').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
</script>
</body>
</html>

