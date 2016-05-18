<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	preg_match("/[a-z0-9_-]{5,20}/", "", $matches);
	print ($matches[0] == "" && !empty($matches[0]));
?>
<html>
	<head>
		<meta charset="utf-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
		
		<script src="//code.jquery.com/jquery-1.12.3.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script>
		
		</script>
	</body>
</html>
<?
include("footer.php");
?>