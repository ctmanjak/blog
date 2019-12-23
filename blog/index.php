<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	unset($owner, $owner_id);
	session_register('owner', 'owner_id');
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	//owner
	//$owner = basename(dirname(__FILE__));
	$owner = $_GET['owner'];
	$post_id = $_GET['post_id'];
	$sql = mysql_query("select user.id, nickname, blog_title, introduce from user left join user_info on user.id=user_info.id where username='$owner'");
	$data = mysql_fetch_array($sql);
	if(empty($data))
	{
		echo "<meta charset='UTF-8'>";
		echo "존재하지 않는 페이지입니다.";
		exit;
	}
	$owner_id = $data[id];
	$owner_nick = $data[nickname];
	$owner_title = $data[blog_title];
	$introduce = $data[introduce];
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user_info.id=$owner_id");
	$data = mysql_fetch_array($sql);
	foreach($data as $key => $value)	if(!is_numeric($key)) $returndata[$key] = $value;
	extract($returndata);
	if($profilepic)
	if(!empty($logged))
	{
		//log_id
		$sql = mysql_query("select user.id, username, nickname from user left join user_info on user.id=user_info.id where user_info.id=$log_id");
		$data = mysql_fetch_array($sql);
		$log_nick = $data[nickname];
		$log_name = $data[username];
		/*$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user_info.id=$log_id");
		$data = mysql_fetch_array($sql);*/
		foreach($data as $key => $value) if(!is_numeric($key)) $returndata[$key] = $value;
		extract($returndata);
		//bookmark
		$sql = mysql_query("select * from bookmark where user_id='$log_id'");
		while($data = mysql_fetch_array($sql))
		{
			$sql2 = mysql_query("select username from user where id=$data[bookmark_id]");
			$data2 = mysql_fetch_array($sql2);
			$bookmark[$data2[username]] = $data[bookmark_name];
		}
	}
	//board
	$sql = mysql_query("select * from board_name where user_id='$owner_id' order by board_id");
	while($data = mysql_fetch_array($sql))
	{
		$board[$data[board_id]] = $data[board_name];
	}
		
	if($logged == 1)
	{
		if(!empty($bookmark))
		{
			foreach($bookmark as $id => $name)
			{
				if($id == $owner)
				{
					$inbookmark = 1;
					break;
				}
				else $inbookmark = 0;
			}
		}
		else $inbookmark = 0;
	}
	else
	{
		$inbookmark = 0;
		$log_id = -1;
	}
	if(!empty($returl))
	{
		print "<meta http-equiv='refresh' content='0; url=../$returl'>";
	}
	if(!empty($post_id))
	{
		$sql = mysql_query("select board_id from board where user_id=$owner_id and post_id=$post_id"); 
		$data = mysql_fetch_array($sql);
		$board_id = $data[board_id];?>
		<script src="../js/jquery.min.js"></script>
		<script>
			$(document).ready(function()
			{
				$(".board_list").removeClass("selected");
				$("#board_id_<?=$board_id?>").addClass("selected");
				$('#board').attr("src", "board.php?readpost=1&post_id=<?=$post_id?>");
			});
		</script>
	<?}
	include("../header.php");
?>
<html>
	<head>
		<title><?=$owner_title?></title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		<script src="../js/jquery.min.js"></script>
		<script src="../js/jquery-ui.min.js"></script>
		<script src="../logout.js"></script>
	</head>
	<body style="background-image:url('../item/bg/<?=$bgpic?>');background-attachment:fixed;">
		<div class="frame">
			<div class="headline" style="<?if($titlepic != "deftitle.png") echo "background-image:url('../item/title/".$titlepic."')";?>background-color:<?$color=explode(",",$head_color);echo $color[0];?>">
				<!--<img src="../item/title/<?=$titlepic?>">-->
				<a href="./<?=$owner?>"><span style="color:<?$color=explode(",",$head_color);echo $color[1];?>"><?=$owner_title?></span></a>
			</div>
			<div class="main">
				<div class="my_info" style="color:<?$color=explode(",",$info_color);echo $color[1];?>">
					<div class="my_picture">
						<img src="../item/profile/<?=$profilepic?>">
					</div>
					<div class="my_profile"><?=$owner_nick?><br></div>
					<div class="introduce">
						<?=$introduce?>
					</div>
					<?
					if($logged==1)
					{
						if ($log_id == $owner_id)
						{?>
							<div class="my_option"><a href="../settings.php" style="color:<?$color=explode(",",$info_color);echo $color[1];?>;">관리</a></div>
						<?}
						else
						{
							if($inbookmark == 1)
							{?>
								<div class="my_option inbookmark"><a href="#" onclick="updatebookmark(event);" style="color:<?$color=explode(",",$info_color);echo $color[1];?>">즐겨찾기 제거</a></div>
							<?}
							else
							{?>
								<div class="my_option"><a href="#" onclick="updatebookmark(event);" style="color:<?$color=explode(",",$info_color);echo $color[1];?>">즐겨찾기</a></div>
							<?}?>
						<?}
					}?>
					<div style="z-index:-1;top:0;position:absolute;width:100%;height:100%;background-color:<?$color=explode(",",$info_color);echo $color[0];?>;opacity:<?$color=explode(",",$info_color);echo $color[2];?>"></div>
				</div>
				<div class="board_name">
					<ul>
						<a href='board.php' target='frame_board'><li align='left' class='board_list selected' id='board_all' style="color:<?$color=explode(",",$boardn_color);echo $color[1];?>">전체 글 보기</li></a>
						<?
							foreach($board as $id => $name)
							{
								print "<a href='board.php?board_id=$id' target='frame_board'><li align='left' class='board_list' id='board_id_$id' style='color:$color[1]'>$name</li></a>";
							}
						?>
					</ul>
					<div style="z-index:-1;top:0;position:absolute;width:100%;height:100%;background-color:<?$color=explode(",",$boardn_color);echo $color[0];?>;opacity:<?$color=explode(",",$boardn_color);echo $color[2];?>;"></div>
				</div>
				<iframe src="board.php" name="frame_board" class="board" id="board" onload="board_ready();check_board();board_color();board_resize();" scrolling="no"></iframe>
				<script>
				var board_ready = function()
				{
					$('#board').contents()[0].getElementsByClassName('board_main')[0].className="board_main";
				}
				var board_color = function()
				{
					var color = '<?$color=explode(",",$board_color);echo $color[0];?>';
					var opacity = '<?$color=explode(",",$board_color);echo $color[2];?>'
					var r = parseInt(color.substr(1, 2),16);
					var g = parseInt(color.substr(3, 4),16);
					var b = parseInt(color.substr(5, 6),16);
					//$('#board').contents()[0].getElementsByClassName('board_table')[0].style.color = '<?$color=explode(",",$board_color);echo $color[1];?>';
					$('#board').contents()[0].getElementsByTagName('body')[0].style.color = '<?$color=explode(",",$board_color);echo $color[1];?>';
					if($('#board').contents()[0].getElementsByTagName('textarea').length != 0)
					{
						for(var i = 0; i < $('#board').contents()[0].getElementsByTagName('textarea').length; i++)
						{
							$('#board').contents()[0].getElementsByTagName('textarea')[i].style.color = '<?$color=explode(",",$board_color);echo $color[1];?>';
							$('#board').contents()[0].getElementsByTagName('textarea')[i].style.background = "rgba("+r+", "+g+", "+b+", "+opacity+")";
						}
					}
					if($('#board').contents()[0].querySelectorAll('input[type=text]').length != 0)
					{
						for(var i = 0; i < $('#board').contents()[0].querySelectorAll('input[type=text]').length; i++)
						{
							$('#board').contents()[0].querySelectorAll('input[type=text]')[i].style.color = '<?$color=explode(",",$board_color);echo $color[1];?>';
							$('#board').contents()[0].querySelectorAll('input[type=text]')[i].style.background = "rgba("+r+", "+g+", "+b+", "+opacity+")";
						}
					}
					$('#board').contents()[0].getElementsByClassName('board_bg')[0].style.backgroundColor = '<?$color=explode(",",$board_color);echo $color[0];?>';
					$('#board').contents()[0].getElementsByClassName('board_bg')[0].style.opacity = '<?$color=explode(",",$board_color);echo $color[2];?>';
					for(var i = 0; i < $('#board').contents()[0].getElementsByTagName('a').length; i++)
					{
						$('#board').contents()[0].getElementsByTagName('a')[i].style.color = '<?$color=explode(",",$board_color);echo $color[1];?>';
					}
					if($('#board').contents()[0].getElementsByClassName('board_table').length != 0)
					{
						for(var i = 0; i < $('#board').contents()[0].getElementsByClassName('board_table')[0].getElementsByTagName('a').length; i++)
						{
							$('#board').contents()[0].getElementsByClassName('board_table')[0].getElementsByTagName('a')[i].style.color='<?$color=explode(",",$board_color);echo $color[1];?>';
						}
					}
				}
				var board_resize = function()
				{
					$('#board').css("height", $('#board').contents()[0].getElementsByClassName('board_main')[0].offsetHeight+"px");
				}
				var check_board = function()
				{
					if($("#board_all").hasClass("selected"))
					{
						$.ajax({
							url:"check_board.php",
							dataType:"json",
							type:"post",
							data:{url:$("#board").contents()[0].location['search'], owner:'<?=$owner?>'},
							success:function(result)
							{
								if(result['result'] == true)
								{
									$(".board_list").removeClass("selected");
									$("#board_id_"+result['board_id']).addClass("selected");
								}
							}
						});
					}
				}
				</script>
			</div>
		</div>
		<script>
			var inbookmark = <?=$inbookmark?>;
			var updatebookmark = function (event)
			{
				$.ajax({
					url:"updatebookmark.php",
					dataType:"json",
					type:"POST",
					data:{user_id:<?=$log_id?>, bookmark_id:<?=$owner_id?>, inbookmark:inbookmark}
				});
				if($('.my_option').hasClass("inbookmark"))
				{
					$('.my_option').removeClass("inbookmark");
					$(".my_option a").text("즐겨찾기");
				}
				else
				{
					$('.my_option').addClass("inbookmark");
					$(".my_option a").text("즐겨찾기 제거");
				}
				$(".bookmark").load("../footer.php .bookmark > *");
				inbookmark = inbookmark==1?0:1;
				event.preventDefault();
			}
			$(".board_list").on("click", function(event)
			{
				$(".board_list").removeClass("selected");
				$(this).addClass("selected");
			});
			$(document).keydown(function(event)
			{
				/*if(event.keyCode==116)
				{
					event.preventDefault();
					location.href=location.href.split("?")[0];
				*/
			});
		</script>
	</body>
</html>
<?
include("../footer.php");
?>