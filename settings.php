<?
	include("config.cfg");
	session_start();
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user.id='$log_id'");
	$data = mysql_fetch_array($sql);
	if($editpwd==2)
	{
		if($cur_pwd != $data[pwd])
		{
			print "<script>alert('비밀번호가 틀립니다.');</script>";
			print "<script>history.go(-2)</script>";
		}
		else 
		{	
			$user_pwd=mysql_real_escape_string($user_pwd);
			mysql_query("update user set pwd='$user_pwd' where id='$log_id'");
		}
		print "<script>alert('비밀번호가 변경되었습니다.');</script>";
		print "<script>history.go(-2)</script>";
	}
?>
<html>
	<head>
		<title>설정</title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		<script src="logout.js"></script>
	</head>
	<body>
		<div class="header">
			<div class="headerlink">
				<ul>
					<a href="./"><li>메인</li></a> l <a href="<?=$log_name?>/"><li>내 블로그</li></a> l <a href="#" onclick="logout(event)"><li>로그아웃</li></a>
				</ul>
			</div>
		</div>
		<div class="setting">
		<?
		if($editpwd==1)
		{?>
			<form method="post" action="settings.php">
				<input type="hidden" name="editpwd" value="2">
				현재 비밀번호 : <input type="password" name="cur_pwd" pattern="[^ \t\r\n\v\f]{5,20}"><br>
				바꿀 비밀번호 : <input type="password" name="user_pwd" pattern="[^ \t\r\n\v\f]{5,20}" title="5~20자의 영문 대 소문자, 숫자와 공백을 제외한 특수문자만 사용 가능합니다."><br>
				<input type="submit" value="변경">
			</form>
		<?}
		else
		{?>
			<ul>
				<a href="settings.php?editpwd=1"><li>비밀번호 변경</li></a>
				<a href="unregister.php"><li>회원탈퇴</li></a>
			</ul>
		<?}?>
		</div>
	</body>
</html>
<?
include("footer.php");
?>