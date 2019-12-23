<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	$sql = mysql_query("select point from user_info where id=$log_id");
	$data = mysql_fetch_array($sql);
	$gamemoney = $data[point];
	include("../header.php");	
?>
<html>
	<head>
		<meta charset="utf-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		<link href='https://fonts.googleapis.com/css?family=Orbitron:900' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<div class="game_frame">
			
			<div class="click_button"></div>
		</div>
		<script src="//<?=HOST?>/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/js/jquery-ui.min.js"></script>
		<script>
			var money = <?=$gamemoney?>;
			
			$(".click_button").text(money);
			$(".click_button").on({mousedown:function(event)
			{
				event.preventDefault();
				$(this).css("font-size", "130px");
			},mouseup:function(event)
			{
				event.preventDefault();
				$(this).css("font-size", "150px");
				$(".click_button").text(++money);
			},mouseleave:function(event)
			{
				event.preventDefault();
				$(this).css("font-size", "150px");
			}
			});
			$(window).on('beforeunload', function(event)
			{
				$.ajax({
					url:"save.php",
					dataType:"json",
					type:"post",
					data:{money:money},
					success:function(result)
					{
						
					}
				});
			});
		</script>
	</body>
</html>
<?
include("../footer.php");
?>