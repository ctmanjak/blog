<?
	session_start();
	include $_SERVER["DOCUMENT_ROOT"]."/blog/config.cfg";
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	//print_r($_SERVER);
?>
<html>
	<head>
		<meta charset="utf-8">
		<link type="text/css" href="http://<?=HOST?>/blog/header.css" rel="stylesheet">
	</head>
	<body>
		<div class="header hide">
			<div class="headerlink">
				<ul>
				<?
				if($_SERVER['PHP_SELF'] != "/blog/index.php"){?><a href="http://<?=HOST?>/blog/"><li>메인</li></a> l<?}
				if($logged == 1)
				{?>
					<?if($owner_id != $log_id || $_SERVER['PHP_SELF'] != "/blog/blog/index.php"){?><a href="http://<?=HOST?>/blog/blog/index.php?owner=<?=$log_name?>"><li>내 블로그</li></a> l<?}?>
					<?if($_SERVER['PHP_SELF'] != "/blog/store.php"){?><a href="http://<?=HOST?>/blog/store.php"><li>상점</li></a> l<?}?>
					<?if($_SERVER['PHP_SELF'] != "/blog/game/index.php"){?><a href="http://<?=HOST?>/blog/game/"><li>게임</li></a> l<?}?>
					<?if($_SERVER['PHP_SELF'] != "/blog/settings.php"){?><a href="http://<?=HOST?>/blog/settings.php"><li>관리</li></a> l<?}?>
					<a href="#" id="logout" onclick="logout(event)"><li>로그아웃</li></a>
				<?}
				else
				{?>
					<li id="login_link">로그인</li>
					<?if($_SERVER['PHP_SELF'] != "/blog/register.php"){?>l <a href="http://<?=HOST?>/blog/register.php"><li>회원가입</li></a><?}?>
				<?}?>
				</ul>
			</div>
		</div>
		<div class="login_frame hide">
			<div class="login_up_border"></div>
			<div class="login_up"></div>
			<div class="login_error"></div>
			아이디 : <input type="text" id="user_name" autocomplete="off"><br>
			비밀번호 : <input type="password" id="user_pwd"><br>
			<input type="button" id="login" value="로그인">
		</div>
		<script src="//<?=HOST?>/blog/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/blog/js/jquery-ui.min.js"></script>
		<script>
			$(document).ready(function()
			{
				$(".header").removeClass("hide");
			});
			$("#login_link").click(function(event)
			{
				event.stopPropagation();
				$(".login_frame").toggle("fade", 100);
			});
			$(".login_frame").click(function(event)
			{
				event.stopPropagation();
			});
			$("#user_name").keyup(function(event)
			{
				if(event.keyCode == 13)
				{
					$("#user_pwd").focus();
				}
			});
			$("body").click(function(event)
			{
				if($(".login_frame").css("display") != "none") $(".login_frame").toggle("fade", 100);
			});
			$("#user_pwd").keyup(function(event)
			{
				if(event.keyCode == 13)
				{
					$("#login").click();
				}
			});
			$("#login").click(function(event)
			{
				$.ajax({
					url:"http://<?=HOST?>/blog/login.php",
					dataType:"json",
					type:"post",
					data:{login:1,user_name:$("#user_name").val(),user_pwd:$("#user_pwd").val()},
					success:function(result)
					{
						if(result['error'] == 0) location.href=location.href;
						else if(result['error'] == 1)
						{
							$(".login_error").css("color","#f00");
							$(".login_error").text("존재하지 않는 아이디입니다.");
						}
						else if(result['error'] == 2)
						{
							$(".login_error").css("color","#f00");
							$(".login_error").text("비밀번호를 잘못 입력하셨습니다.");
						}
					}
				});
			});
			$("#logout").click(function(event)
            {
                event.preventDefault();
                $.ajax({
                    url:"http://<?=HOST?>/blog/logout.php",
                    dataType:"json",
                    type:"post",
                    success:function(result)
                    {
                        location.href="http://<?=HOST?>/blog/";
                    }
                });
            });
		</script>
	</body>
</html>