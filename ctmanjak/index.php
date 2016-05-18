<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	unset($owner, $owner_id);
	session_register('owner', 'owner_id');
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	//owner
	$owner = basename(dirname(__FILE__));
	$sql = mysql_query("select user.id, nickname, blog_title, introduce from user left join user_info on user.id=user_info.id where username='$owner'");
	$data = mysql_fetch_array($sql);
	$owner_id = $data[id];
	$owner_nick = $data[nickname];
	$owner_title = $data[blog_title];
	$introduce = $data[introduce];
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user_info.id=$owner_id");
	$data = mysql_fetch_array($sql);
	foreach($data as $key => $value)	if(!is_numeric($key)) $returndata[$key] = $value;
	extract($returndata);
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
	else $inbookmark = 0;
	if(!empty($returl))
	{
		print "<meta http-equiv='refresh' content='0; url=../$returl'>";
	}
	if(!empty($post_id))
	{
		$sql = mysql_query("select board_id from board where user_id=$owner_id and post_id=$post_id"); 
		$data = mysql_fetch_array($sql);
		$board_id = $data[board_id];?>
		<script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
		<script>
			$(document).ready(function()
			{
				$(".board_list").removeClass("selected");
				$("#board_id_<?=$board_id?>").addClass("selected");
				$('#board').attr("src", "board.php?readpost=1&post_id=<?=$post_id?>");
			});
		</script>
	<?}
?>
<html>
	<head>
		<title><?=$owner_title?></title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		<script src="//code.jquery.com/jquery-1.12.3.min.js"></script>
		<script src="../logout.js"></script>
	</head>
	<body style="background-image:url('<?=$bgpic?>')">
		<div class="header">
			<div class="headerlink">
				<ul><a href="../"><li>메인</li></a>
				<?if ($logged == 1) 
				{?>
					l <a href="../store.php"><li>상점</li></a> l <a href="../game/"><li>게임</li></a> l <a href="../<?=$log_name?>"><li>내 블로그</li></a> l <a href="#" onclick="logout(event)"><li>로그아웃</li></a>
				<?}?>
				</ul>
			</div>
		</div>
		<div class="frame">
			<div class="headline">
				<img src="<?=$headpic?>">
				<a href="./"><span><?=$owner_title?></span></a>
			</div>
			<div class="main">
				<div class="my_info">
					<div class="my_picture">
						<img src="<?=$profilepic?>">
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
							<div class="my_option"><a href="settings.php">관리</a></div>
						<?}
						else
						{?>
							<?if($inbookmark == 1)
							{?>
								<div class="my_option"><a href="#" onclick="updatebookmark(event);">즐겨찾기 제거</a></div>
							<?}
							else
							{?>
								<div class="my_option"><a href="#" onclick="updatebookmark(event);">즐겨찾기</a></div>
							<?}?>
						<?}
					}?>
					<script>
					</script>
				</div>
				<iframe src="board.php" name="frame_board" class="board" id="board" onload="board_resize()" scrolling="no"></iframe>
				<script>
				var board_resize = function()
				{
					$('#board').css("height", $('#board').contents()[0].getElementsByClassName('board_main')[0].offsetHeight+80);
				}
				</script>
				<div class="board_name">
					<ul>
						<a href='board.php' target='frame_board'><li align='left' class='board_list selected'>전체 글 보기</li></a>
						<?
							foreach($board as $id => $name)
							{
								print "<a href='board.php?board_id=$id' target='frame_board'><li align='left' class='board_list' id='board_id_$id'>$name</li></a>";
							}
						?>
					</ul>
				</div>
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
				$('.my_option').load("index.php .my_option>*");
				$('.bookmark').load("index.php .bookmark>*");
				inbookmark = inbookmark==1?0:1;
				event.preventDefault();
			}
			$(".board_list").on("click", function(event)
			{
				$(".board_list").removeClass("selected");
				$(this).addClass("selected");
			});
			
		</script>
	</body>
</html>
<?
include("../footer.php");
?>