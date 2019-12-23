<?
	session_start();
	include("../config.cfg");
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
		<ul id="image_list" style="border:1px solid;height:150px;overflow:auto;">
		
		</ul>
		<input type="file" id="image" accept="image/*">
		<script src="//<?=HOST?>/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/js/jquery-ui.min.js"></script>
		<script>
			$("#image").change(function(event)
			{
				var reader = new FileReader();
				reader.readAsDataURL($(this)[0].files[0]);
				reader.onload = function()
				{
					$("#image_list").append("<li style='height:50px;list-style-type:none;'><img src="+reader.result+" style='float:left;height:100%;width:auto;'><div style='position:relative;top:50%;transform:translate(0,-50%)'>"+$("#image")[0].files[0]['name']+"</div></li>");
					$("#image").val("");
				}
			});
		</script>
	</body>
</html>