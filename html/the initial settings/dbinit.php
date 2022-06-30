<?php
header('Content-Type: text/html; charset=UTF-8');

error_reporting(E_ALL); 
ini_set('display_errors',1); 
$_SERVER['DOCUMENT_ROOT'];
include('../subpage/check.php');



if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])){
	$password=$_POST['password'];
	$db_password = '114721141';

	if($password != $db_password){
		$errMSG = '비밀번호 오류';
	}
	if(!isset($errMSG)){
		$databaseName = 'harang9100';
		$databaseUser = 'harang9100';
		$databasePassword = 'newtaling20!';


		$pdoDatabase = new PDO('mysql:host=localhost', $databaseUser, $databasePassword);
		$pdoDatabase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdoDatabase->exec('DROP DATABASE IF EXISTS harang9100;');
		$pdoDatabase->exec('CREATE DATABASE IF NOT EXISTS harang9100 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

		$pdo = new PDO('mysql:host=localhost;dbname='.$databaseName, $databaseUser, $databasePassword);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		/*
		DB 구조 정의 명세서 (스프레드시트)
		https://docs.google.com/spreadsheets/d/1WgEcjoURyzYCCOUh5T5M3WHiQ9XDAAl9S8vhfar_-ks/edit?usp=sharing
		*/
		$pdo->exec('CREATE TABLE `users` (
			`uid` int(11) NOT NULL COMMENT "PRIMARY KEY" AUTO_INCREMENT,
			`id` varchar(255) NOT NULL,
			`pw` varchar(255) NOT NULL,
			`salt` varchar(255) NOT NULL,
			`username` varchar(255),
			`userprofile` varchar(255),
			`authority` int(11) NOT NULL,
			`team` int(11) NOT NULL DEFAULT 0,
			`startday` date NOT NULL,
			`D-DAY` int(11) NOT NULL DEFAULT 0,
			`activate` tinyint(4) NOT NULL DEFAULT 1, 
		 	`user_regtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`uid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');

		$pdo->exec('CREATE TABLE `vacation`(
			`vacid` int(11) NOT NULL AUTO_INCREMENT, 
		 	`uid` int(11),
		 	`va_case` int(11) NOT NULL, 
		 	`userprofile` varchar(255),
		 	`va_day_start` date NOT NULL, 
			`va_day_detail` int(11),
			`detail` varchar(255),
			`activate` tinyint(4) NOT NULL DEFAULT 1, 
			`correction_time` datetime,
		 	`vac_regtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`vacid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');


		echo "db 초기화에 성공했습니다.\n";
	}
	else{
	    echo "<SCRIPT type='text/javascript'> //not showing me this
	        alert('$errMSG');
	    </SCRIPT>"; 
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>데이터초기화</title>
</head>
<body>
	<form class="form_login mt-50" method="POST">
		<div class="form form_pw">
	        <!--<label for="user_password"> 패스워드  : </label>-->
	        <input type="password" name="password" class="form_control  invalid" id="inputPW" placeholder="password"required  autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" />
	    </div>
	    <div class="form pt-60">
	        <button class="button_efect" type="submit" name="submit">입력</button>
	    </div>
    </form>
</body>
</html>