<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	unset($owner, $owner_id);
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user.id='$log_id'");
	$data = mysql_fetch_array($sql);
?>
<html>
	<head>
		<title>홈</title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		<script src="logout.js"></script>
	</head>
	<body>
		<div class="header">
			<div class="headerlink">
				<?
				if($logged == 1)
				{?>
					<ul><a href="store.php"><li>상점</li></a> l <a href="game/"><li>게임</li></a> l <a href="<?=$log_name?>/"><li>내 블로그</li></a> l <a href="settings.php"><li>설정</li> l <a href="#" onclick="logout(event)"><li>로그아웃</li></a></ul>
				<?}
				else
				{?>
					<ul><a href="login.php"><li>로그인</li></a> l <a href="register.php"><li>회원가입</li></a></ul>
				<?}?>
			</div>
		</div>
		<div class="main">
			<center><a href="./"><div class="logo"></div></a><p>
			<div class="searchbar">
				<form method="get" action="search.php">
					<input type="hidden" name="category" value="<?=ALL?>">
					<input type="text" name="search" autocomplete="off"><input type="submit" value="검색">
				</form>
			</div>
		</div>
	</body>
</html>
<?
include("footer.php");
?>