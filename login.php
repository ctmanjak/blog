<?
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	session_register('logged','log_id');
	
	if($login == 1)
	{
		$sql = mysql_query("select * from user where username='$user_name'");
		$data = mysql_fetch_array($sql);
		if(!empty($data))
		{
			if($data[pwd] == $user_pwd)
			{
				$logged=1;
				$log_id=$data[id];
				$_SESSION['log_name']=$data[username];
				print "<script>history.go(-2)</script>";
			}
			else print "<script>alert('비밀번호가 틀립니다.');</script>";
		}
		else print "<script>alert('존재하지 않는 아이디입니다.');</script>";
	}
	
	mysql_close();
?>

<html>
	<head>
		<title>로그인</title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
		<div class="header">
			<div class="headerlink">
				<ul><a href="./"><li>메인</li></a></ul>
			</div>
		</div>
		<form method="post" action="login.php">
			<input type="hidden" name="login" value="1">
			아이디 : <input type="text" name="user_name" autocomplete="off"><br>
			비밀번호 : <input type="password" name="user_pwd"><br>
			<input type="submit" value="로그인">
		</form>
	</body>
</html>