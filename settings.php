<?
	include("config.cfg");
	session_start();
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user.id='$log_id'");
	$data = mysql_fetch_array($sql);
	$head_color = $data[head_color];
	$info_color = $data[info_color];
	$board_color = $data[board_color];
	$boardn_color = $data[boardn_color];
	if($editpwd==2)
	{
		if($cur_pwd != $data[pwd])
		{
			print "<script>alert('비밀번호가 틀립니다.');</script>";
			print "<script>history.go(-2)</script>";
		}
		else 
		{	
			$user_pwd=mysql_real_escape_string($user_pwd);
			mysql_query("update user set pwd='$user_pwd' where id='$log_id'");
		}
		print "<script>alert('비밀번호가 변경되었습니다.');</script>";
		print "<script>history.go(-2)</script>";
	}
	else if($editprofile==2)
	{
		$blog_title=strip_tags($blog_title);
		$blog_title=htmlspecialchars($blog_title);
		$nickname=strip_tags($nickname);
		$nickname=htmlspecialchars($nickname);
		$introduce=strip_tags($introduce);
		$introduce=htmlspecialchars($introduce);
		foreach($color as $key => $val)
		{
			$type = explode("_",$key);
			if($type[0] == "head") $mod_head_color[] = $val;
			else if($type[0] == "info") $mod_info_color[] = $val;
			else if($type[0] == "boardn") $mod_boardn_color[] = $val;
			else if($type[0] == "board") $mod_board_color[] = $val;
		}
		$mod_head_color[] = $head_color_opacity/100;
		$mod_info_color[] = $info_color_opacity/100;
		$mod_boardn_color[] = $boardn_color_opacity/100;
		$mod_board_color[] = $board_color_opacity/100;
		$mod_head_color = implode(",",$mod_head_color);
		$mod_info_color = implode(",",$mod_info_color);
		$mod_boardn_color = implode(",",$mod_boardn_color);
		$mod_board_color = implode(",",$mod_board_color);
		mysql_query("update user_info set blog_title='$blog_title', nickname='$nickname', introduce='$introduce' where id='$log_id'");
		mysql_query("update user_info set head_color='$mod_head_color', info_color='$mod_info_color', board_color='$mod_board_color', boardn_color='$mod_boardn_color' where id='$log_id'");
		print "<script>history.go(-2);</script>";
	}
	else if($editgroup==2)
	{
		if(!empty($board_add_sel) && $addgroup == 1)
		{
			foreach($board_add as $board_id => $board_name)
			{
				if(!empty($board_name))
				{
					$board_name=strip_tags($board_name);
					$board_name=htmlspecialchars($board_name);
					mysql_query("insert into board_name(user_id, board_id, board_name) values('$log_id', '$board_id', '$board_name')");
				}
			}
		}
		if(!empty($board_sel))
		{
			foreach($board_sel as $id)
			{
				if (!empty($board_mod[$id])) 
				{
					$board_mod[$id]=strip_tags($board_mod[$id]);
					$board_mod[$id]=htmlspecialchars($board_mod[$id]);
					mysql_query("update board_name set board_name='$board_mod[$id]' where board_id='$id' and user_id='$log_id'");
				}
				else 
				{
					$sql = mysql_query("select count(*) as board_count from board_name where user_id='$log_id'");
					$data = mysql_fetch_array($sql);
					if($data[board_count] == 1)
					{
						print "<script>alert('게시판은 하나 이상 있어야합니다. 마지막 게시판은 삭제되지 않습니다.')</script>";
						break;
					}
					mysql_query("delete from board_name where user_id='$log_id' AND board_id='$id'");
					mysql_query("delete from board where user_id='$log_id' AND board_id='$id'");
				}
			}
			print "<script>history.go(-2);</script>";
			exit;
		}
		print "<script>history.go(-2);</script>";
	}
	else if($editbookmark==2)
	{
		if(!empty($bookmark_sel))
		{
			foreach($bookmark_sel as $id)
			{
				if (!empty($bookmark_mod[$id]))
				{
					$bookmark_mod[$id]=strip_tags($bookmark_mod[$id]);
					$bookmark_mod[$id]=htmlspecialchars($bookmark_mod[$id]);
					mysql_query("update bookmark set bookmark_name='$bookmark_mod[$id]' where bookmark_id='$id' and user_id='$log_id'");
				}
				else 
				{
					mysql_query("delete from bookmark where user_id='$log_id' AND bookmark_id='$id'");
				}
			}
		}
		print "<script>history.go(-2);</script>";
	}
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user.id='$log_id'");
	$data = mysql_fetch_array($sql);
	include("header.php");
?>
<html>
	<head>
		<title>계정&블로그 관리</title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		<script src="logout.js"></script>
	</head>
	<body>
		<div class="setting">
			<div class="setting_main">
				<?
				if($editpwd==1)
				{?>
					<form method="post" action="settings.php">
						<input type="hidden" name="editpwd" value="2">
						현재 비밀번호 : <input type="password" name="cur_pwd" pattern="[^ \t\r\n\v\f]{5,20}"><br>
						바꿀 비밀번호 : <input type="password" name="user_pwd" pattern="[^ \t\r\n\v\f]{5,20}" title="5~20자의 영문 대 소문자, 숫자와 공백을 제외한 특수문자만 사용 가능합니다."><br>
						<input type="submit" value="변경">
					</form>
				<?}
				else if($editprofile==1)
				{?>
					<form method="post" action="settings.php">
						<input type="hidden" name="editprofile" value="2">
						블로그 이름 : <input type="text" autocomplete="off" name="blog_title" value="<?=$data[blog_title]?>"><br>
						닉네임 : <input type="text" autocomplete="off" name="nickname" value="<?=$data[nickname]?>"><br>
						소개말 : <input type="text" autocomplete="off" name="introduce" value="<?=$data[introduce]?>"><br>
						타이틀 배경색 : <input type="color" name="color[head_color]" value="<?$color=explode(",",$head_color);echo $color[0];?>"> 불투명도 : <input type="range" name="head_color_opacity" min="0" max="100" step="5" value="<?$color=explode(",",$head_color);echo $color[2]*100;?>"><div style="display:inline"> <?$color=explode(",",$head_color);echo $color[2]*100;?>%</div><br><br>
						타이틀 글자색 : <input type="color" name="color[head_color_txt]" value="<?$color=explode(",",$head_color);echo $color[1];?>"><br>
						내정보 배경색 : <input type="color" name="color[info_color]" value="<?$color=explode(",",$info_color);echo $color[0];?>"> 불투명도 : <input type="range" name="info_color_opacity" min="0" max="100" step="5" value="<?$color=explode(",",$info_color);echo $color[2]*100;?>"><div style="display:inline"> <?$color=explode(",",$info_color);echo $color[2]*100;?>%</div><br>
						내정보 글자색 : <input type="color" name="color[info_color_txt]" value="<?$color=explode(",",$info_color);echo $color[1];?>"><br>
						게시판 목록 배경색 : <input type="color" name="color[boardn_color]" value="<?$color=explode(",",$boardn_color);echo $color[0];?>"> 불투명도 : <input type="range" name="boardn_color_opacity" min="0" max="100" step="5" value="<?$color=explode(",",$boardn_color);echo $color[2]*100;?>"><div style="display:inline"> <?$color=explode(",",$boardn_color);echo $color[2]*100;?>%</div><br>
						게시판 목록 글자색 : <input type="color" name="color[boardn_color_txt]" value="<?$color=explode(",",$boardn_color);echo $color[1];?>"><br>
						게시판 배경색 : <input type="color" name="color[board_color]" value="<?$color=explode(",",$board_color);echo $color[0];?>"> 불투명도 : <input type="range" name="board_color_opacity" min="0" max="100" step="5" value="<?$color=explode(",",$board_color);echo $color[2]*100;?>"><div style="display:inline"> <?$color=explode(",",$board_color);echo $color[2]*100;?>%</div><br>
						게시판 글자색 : <input type="color" name="color[board_color_txt]" value="<?$color=explode(",",$board_color);echo $color[1];?>"><br>
						<input type="submit" value="수정">
					</form>
					<script>
						$("input[type=range]").change(function()
						{
							$(this).next().text(" "+$(this).val()+"%");
						});
					</script>
				<?}
				else if($editgroup==1)
				{?>
					아무 것도 입력하지 않으면 게시판을 삭제합니다.<br>게시판에 있는 모든 글이 삭제되니 주의하세요.<br><br>
					<form class="form_editgroup" method="post" action="settings.php">
						<input type="hidden" name="editgroup" value="2">
						<?$sql = mysql_query("select board_id, board_name from board_name where user_id='$log_id' order by board_id");
						while($data = mysql_fetch_array($sql))
						{?>
							<input type="text" autocomplete="off" name="board_mod[<?=$data[board_id]?>]" value="<?=$data[board_name]?>"><input type="checkbox" name="board_sel[]" value="<?=$data[board_id]?>"><br>
						<?$id=$data[board_id];
						}
						?><div></div>
						<input type="submit" value="수정">
					</form>
					<input type="button" id="addgroup" value="추가">
					<script>
					var addgroup = 0;
					var id = <?=$id+1?>;
						$("#addgroup").click(function()
						{
							if(addgroup == 0)
							{
								$("input[type=submit]").prev().append('<input type="hidden" name="addgroup" value="1"><input type="text" autocomplete="off" name="board_add['+id+']"><input type="checkbox" name="board_add_sel[]" value="'+id+'" checked><br>');
								addgroup = 1;
							}
							else
							{
								id++;
								$("input[type=submit]").prev().append('<input type="text" autocomplete="off" name="board_add['+id+'>]"><input type="checkbox" name="board_add_sel[]" value="'+id+'" checked><br>');
							}
						});
					</script>
				<?}
				else if($editbookmark==1)
				{?>
					아무 것도 입력하지 않으면 즐겨찾기를 삭제합니다.<br><br>
					<form method="post" action="settings.php">
					<input type="hidden" name="editbookmark" value="2">
					<?$sql = mysql_query("select bookmark_id, bookmark_name, username, nickname, blog_title from user as u, user_info as ui, bookmark as b where u.id=ui.id and ui.id=b.bookmark_id and b.user_id='$log_id'");
						while($data = mysql_fetch_array($sql))
						{?>
							<input type="text" autocomplete="off" name="bookmark_mod[<?=$data[bookmark_id]?>]" value="<?=$data[bookmark_name]?>"><input type="checkbox" name="bookmark_sel[]" value="<?=$data[bookmark_id]?>"><br>
						<?}?>
						<input type="submit" value="수정">
					</form>
				<?}
				else
				{?>
					<ul>
						<a href="http://<?=HOST?>/2016Web/1524023/blog/inventory.php"><li>소유하고 있는 아이템</li></a>
						<a href="settings.php?editprofile=1"><li>프로필 편집</li></a>
						<a href="settings.php?editgroup=1"><li>게시판 그룹 관리</li></a>
						<a href="settings.php?editbookmark=1"><li>즐겨찾기 관리</li></a>
						<a href="settings.php?editpwd=1"><li>비밀번호 변경</li></a>
						<a href="unregister.php"><li>회원탈퇴</li></a>
					</ul>
				<?}?>
			</div>
		</div>
	</body>
</html>
<?
include("footer.php");
?>