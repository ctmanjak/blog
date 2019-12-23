<?
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	if($register == 1)
	{
		//$user_pwd=mysql_real_escape_string($user_pwd);
		$user_pwd=md5($user_pwd);
		mysql_query("insert into user(username, pwd) values('$user_name', '$user_pwd')");
		$sql = mysql_query("select id from user where username='$user_name'");
		$data = mysql_fetch_array($sql);
		mysql_query("insert into user_info(id, nickname, blog_title, introduce, hasitem) values('$data[id]', '$user_name', '$user_name 의 블로그', '안녕하세요. $user_name 입니다', '0')");
		mysql_query("insert into board_name(user_id, board_id, board_name) values('$data[id]', 1, '자유 게시판')");
		print "<script>history.go(-2)</script>";
	}
	else include("header.php");
	mysql_close();
?>
<html>
	<head>
		<title>회원가입</title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
		<form id="register" method="post" action="register.php">
			<input type="hidden" name="register" value="1">
			아이디 : <input type="text" id="reg_user_name" name="user_name" autocomplete="off"><span> </span><br>
			비밀번호 : <input type="password" id="reg_user_pwd" name="user_pwd"><span> </span><br>
			<input type="submit" value="가입하기" id="registerbt">
		</form>
		<script src="//<?=HOST?>/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/js/jquery-ui.min.js"></script>
		<script>
			var name_regex = new RegExp('[a-z0-9_-]{5,20}');
			var pwd_regex = new RegExp('[^ \t\r\n\v\f]{5,20}');
			$("#reg_user_name").on("blur", function(event)
				{
					$.ajax({
						url:"check_regex.php",
						dataType:"json",
						type:"post",
						data:{'chk_name':1, 'user_name':$("#reg_user_name").val()},
						success:function(result)
						{
							if(result['result'] == true)
							{
								if(result['error_type'] == 0)
								{
									$("#reg_user_name").next().replaceWith("<span style='color:#00ff00;font-weight:bold'> 사용가능한 아이디입니다.</span>");
									$("#reg_user_name").addClass("checked");
								}
								else if(result['error_type'] == 1)
								{
									$("#reg_user_name").next().replaceWith("<span style='color:#ff0000;font-weight:bold'> 이미 사용중인 아이디입니다.</span>");
									$("#reg_user_name").removeClass("checked");
								}
								else
								{
									$("#reg_user_name").next().replaceWith("<span style='color:#ff0000;font-weight:bold'> 5~20자의 영문 소문자, 숫자와 특수기호(_),(-)만 사용 가능합니다.</span>");
									$("#reg_user_name").removeClass("checked");
								}
							}
						}
					});
				});
				$("#reg_user_pwd").on("blur", function(event)
				{
					$.ajax({
						url:"check_regex.php",
						dataType:"json",
						type:"post",
						data:{'chk_pwd':1, 'user_pwd':$("#reg_user_pwd").val()},
						success:function(result)
						{
							if(result['result'] == true)
							{
								if(result['error_type'] == 0)
								{
									$("#reg_user_pwd").next().replaceWith("<span style='color:#00ff00;font-weight:bold'> 사용가능한 비밀번호입니다.</span>");
									$("#reg_user_pwd").addClass("checked");
								}
								else
								{
									$("#reg_user_pwd").next().replaceWith("<span style='color:#ff0000;font-weight:bold'> 5~20자의 영문 대 소문자, 숫자와 공백을 제외한 특수문자만 사용 가능합니다.</span>");
									$("#reg_user_pwd").removeClass("checked");
								}
							}
						}
					});
				});
				$("#reg_user_pwd").on("keydown", function(event)
				{
					if(event.keyCode == 13)
					{
						$.ajax({
							url:"check_regex.php",
							dataType:"json",
							type:"post",
							data:{'chk_pwd':1, 'user_pwd':$("#reg_user_pwd").val()},
							success:function(result)
							{
								if(result['result'] == true)
								{
									if(result['error_type'] == 0)
									{
										$("#reg_user_pwd").next().replaceWith("<span style='color:#00ff00;font-weight:bold'> 사용가능한 비밀번호입니다.</span>");
										$("#reg_user_pwd").addClass("checked");
										$("#registerbt").click();
									}
									else
									{
										$("#reg_user_pwd").next().replaceWith("<span style='color:#ff0000;font-weight:bold'> 5~20자의 영문 대 소문자, 숫자와 공백을 제외한 특수문자만 사용 가능합니다.</span>");
										$("#reg_user_pwd").removeClass("checked");
									}
								}
							}
						});
					}
				});
				$("#register").on("submit", function(event)
				{
					if(!($("#reg_user_name").hasClass("checked")) || !($("#reg_user_pwd").hasClass("checked")))
					{
						event.preventDefault();
					}
				});
			</script>
	</body>
</html>