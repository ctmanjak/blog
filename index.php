<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	unset($owner, $owner_id);
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user.id='$log_id'");
	$data = mysql_fetch_array($sql);
	include("header.php");
?>
<html>
	<head>
		<title>홈</title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
		<div class="main hide">
			<center><a href="./"><div class="logo"></div></a><p>
			<div class="searchbar">
				<form method="get" action="search.php">
					<input type="hidden" name="category" value="<?=ALL?>">
					<input type="text" name="search" autocomplete="off"><input type="submit" value="검색">
				</form>
			</div>
		</div>
		<script>
			$(document).ready(function()
			{
				$(".main").removeClass("hide");
			});
			
		</script>
	</body>
</html>
<?
include("footer.php");
?>