<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	if($editprofile==2)
	{
		if($modpp == 1)
		{
			if(!empty($_FILES[profile][name]))
			{
				$check = @getimagesize($_FILES[profile][tmp_name]);
				if($check !== false) $upload = 1;
				else $upload = 0;
				if($upload == 1)
				{
					$sql = mysql_query("select profilepic from user_info where id='$owner_id'");
					$data = mysql_fetch_array($sql);
					unlink($data[profilepic]);
					$source = $_FILES[profile][tmp_name];
					$dest = "./".$_FILES[profile][name];
					$ext = pathinfo($_FILES[profile][name], PATHINFO_EXTENSION);
					$profilepic = "profile.".$ext;
					move_uploaded_file($source, $profilepic);
				}
				else print "<script>alert('이미지 파일만 업로드할 수 있습니다.');history.go(-1)</script>";
			}
			else
			{
				$sql = mysql_query("select profilepic from user_info where id='$owner_id'");
				$data = mysql_fetch_array($sql);
				unlink($data[profilepic]);
				$profilepic = "defprofile.png";
				copy("defprofile.png.bak", $profilepic);
			}
			mysql_query("update user_info set profilepic='$profilepic' where id='$owner_id'");
		}
		$blog_title=strip_tags($blog_title);
		$blog_title=htmlspecialchars($blog_title);
		$nickname=strip_tags($nickname);
		$nickname=htmlspecialchars($nickname);
		$introduce=strip_tags($introduce);
		$introduce=htmlspecialchars($introduce);
		mysql_query("update user_info set blog_title='$blog_title', nickname='$nickname', introduce='$introduce' where id='$owner_id'");
		?>
		<script>
			history.go(-2);
		</script>
		<?
	}
	else if($editblog==2)
	{
		if($modhp == 1)
		{
			if(!empty($_FILES[head][name]))
			{
				$check = @getimagesize($_FILES[head][tmp_name]);
				if($check !== false) $upload = 1;
				else $upload = 0;
				if($upload == 1)
				{
					$sql = mysql_query("select headpic from user_info where id='$owner_id'");
					$data = mysql_fetch_array($sql);
					unlink($data[headpic]);
					$source = $_FILES[head][tmp_name];
					$dest = "./".$_FILES[head][name];
					$ext = pathinfo($_FILES[profile][name], PATHINFO_EXTENSION);
					$headpic = "head.".$ext;
					move_uploaded_file($source, $headpic);
				}
				else print "<script>alert('이미지 파일만 업로드할 수 있습니다.');history.go(-1)</script>";
			}
			else
			{
				$sql = mysql_query("select headpic from user_info where id='$owner_id'");
				$data = mysql_fetch_array($sql);
				unlink($data[headpic]);
				$headpic = "defhead.png";
				copy("defhead.png.bak", $headpic);
			}
			mysql_query("update user_info set headpic='$headpic' where id='$owner_id'");
		}
		if($modbp == 1)
		{
			if(!empty($_FILES[bg][name]))
			{
				$check = @getimagesize($_FILES[bg][tmp_name]);
				if($checc !== false) $upload = 1;
				else $upload = 0;
				if($upload == 1)
				{
					$sql = mysql_query("select bgpic from user_info where id='$owner_id'");
					$data = mysql_fetch_array($sql);
					unlink($data[bgpic]);
					$source = $_FILES[bg][tmp_name];
					$dest = "./".$_FILES[bg][name];
					$ext = pathinfo($_FILES[profile][name], PATHINFO_EXTENSION);
					$bgpic = "bg.".$ext;
					move_uploaded_file($source, $bgpic);
				}
				else print "<script>alert('이미지 파일만 업로드할 수 있습니다.');history.go(-1)</script>";
			}
			else
			{
				$sql = mysql_query("select bgpic from user_info where id='$owner_id'");
				$data = mysql_fetch_array($sql);
				unlink($data[bgpic]);
				$bgpic = "defbg.png";
				copy("defbg.png.bak", $bgpic);
			}
			mysql_query("update user_info set bgpic='$bgpic' where id='$owner_id'");
		}
		?>
		<script>
			history.go(-2);
		</script>
		<?
	}
	else if($editgroup==2)
	{
		if(!empty($board_add) && $addgroup == 1)
		{
			$board_name=strip_tags($board_name);
			$board_name=htmlspecialchars($board_name);
			mysql_query("insert into board_name(user_id, board_id, board_name) values('$owner_id', '$board_add', '$board_name')");
			?>
		<script>
			history.go(-3);
		</script>
		<?
		}
		if(!empty($board_sel))
		{
			foreach($board_sel as $id)
			{
				if (!empty($board_mod[$id])) 
				{
					$board_mod[$id]=strip_tags($board_mod[$id]);
					$board_mod[$id]=htmlspecialchars($board_mod[$id]);
					mysql_query("update board_name set board_name='$board_mod[$id]' where board_id='$id' and user_id='$owner_id'");
				}
				else 
				{
					$sql = mysql_query("select count(*) as board_count from board_name where user_id='$owner_id'");
					$data = mysql_fetch_array($sql);
					if($data[board_count] == 1)
					{
						print "<script>alert('게시판은 하나 이상 있어야합니다. 마지막 게시판은 삭제되지 않습니다.')</script>";
						break;
					}
					mysql_query("delete from board_name where user_id='$owner_id' AND board_id='$id'");
					mysql_query("delete from board where user_id='$owner_id' AND board_id='$id'");
				}
			}
			?>
		<script>
			history.go(-2);
		</script>
		<?
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
					mysql_query("update bookmark set bookmark_name='$bookmark_mod[$id]' where bookmark_id='$id' and user_id='$owner_id'");
				}
				else 
				{
					mysql_query("delete from bookmark where user_id='$owner_id' AND bookmark_id='$id'");
				}
			}
		}
		?>
		<script>
			history.go(-2);
		</script>
		<?
	}
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user.id='$owner_id'");
	$data = mysql_fetch_array($sql);
?>
<html>
	<head>
		<title>블로그 관리</title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		<script src="../logout.js"></script>
	</head>
	<body>
	<div class="frame">
		<div class="header">
			<div class="headerlink">
				<ul><a href="../"><li>메인</li></a> l <a href="../<?=$data[username]?>"><li>내 블로그</li></a> l <a href="#" onclick="logout(event)"><li>로그아웃</li></a></ul>
			</div>
		</div>
		<div class="setting_main">
		<?if($editprofile==1)
		{?>
			<div class="setting_editnotice">사진을 변경하시려면 체크박스에 체크를 해주세요.<br>체크후 아무 파일도 업로드하지 않으면 기본 사진으로 변경됩니다.<br></div>
			<center><div class="setting_notice up"></div></center>
			<form method="post" action="settings.php" enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="1048576">
				<input type="hidden" name="editprofile" value="2">
				블로그 이름 : <input type="text" autocomplete="off" name="blog_title" value="<?=$data[blog_title]?>"><br>
				닉네임 : <input type="text" autocomplete="off" name="nickname" value="<?=$data[nickname]?>"><br>
				소개말 : <input type="text" autocomplete="off" name="introduce" value="<?=$data[introduce]?>"><br>
				프로필 사진 : <img src="<?=$data[profilepic]?>" style="display:block;"><input type="file" name="profile" accept="image/*"><input type="checkbox" name="modpp" value="1"><br><br>
				<input type="submit" value="수정">
			</form>
		<?}
		else if($editblog==1)
		{?>
			<div class="setting_editnotice">사진을 변경하시려면 체크박스에 체크를 해주세요.<br>체크후 아무 파일도 업로드하지 않으면 기본 사진으로 변경됩니다.<br></div>
			<center><div class="setting_notice up"></div></center>
			<form method="post" action="settings.php" enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="1048576">
				<input type="hidden" name="editblog" value="2">
				블로그 로고 사진 : <img src="<?=$data[headpic]?>" style="display:block;max-width:300px"><input type="file" name="head" accept="image/*"><input type="checkbox" name="modhp" value="1"><br>
				블로그 배경 사진 : <img src="<?=$data[bgpic]?>" style="display:block;max-width:300px"><input type="file" name="bg" accept="image/*"><input type="checkbox" name="modbp" value="1"><br><br>
				<input type="submit" value="수정">
			</form>
		<?}
		else if($editgroup==1)
		{?>
			아무 것도 입력하지 않으면 게시판을 삭제합니다.<br>게시판에 있는 모든 글이 삭제되니 주의하세요.<br><br>
			<form method="post" action="settings.php">
				<input type="hidden" name="editgroup" value="2">
				<?$sql = mysql_query("select board_id, board_name from board_name where user_id='$owner_id' order by board_id");
				while($data = mysql_fetch_array($sql))
				{?>
					<input type="text" autocomplete="off" name="board_mod[<?=$data[board_id]?>]" value="<?=$data[board_name]?>"><input type="checkbox" name="board_sel[]" value="<?=$data[board_id]?>"><br>
				<?$id=$data[board_id];
				}
				if($addgroup == 1) 
				{?>
					<input type="hidden" name="addgroup" value="1">
					<input type="text" autocomplete="off" name="board_name"><input type="checkbox" name="board_add" value="<?=$id+1?>"><br>
				<?}?>
				<input type="submit" value="수정">
			</form>
			<form method="post" action="settings.php">
				<input type="hidden" name="editgroup" value="1">
				<input type="hidden" name="addgroup" value="1">
				<?if($addgroup != 1)
					{?>
						<input type="submit" value="추가">
					<?}?>
			</form>
		<?}
		else if($editbookmark==1)
		{?>
			아무 것도 입력하지 않으면 즐겨찾기를 삭제합니다.<br><br>
			<form method="post" action="settings.php">
			<input type="hidden" name="editbookmark" value="2">
			<?$sql = mysql_query("select bookmark_id, bookmark_name, username, nickname, blog_title from user as u, user_info as ui, bookmark as b where u.id=ui.id and ui.id=b.bookmark_id and b.user_id='$owner_id'");
				while($data = mysql_fetch_array($sql))
				{?>
					<input type="text" autocomplete="off" name="bookmark_mod[<?=$data[bookmark_id]?>]" value="<?=$data[bookmark_name]?>"><input type="checkbox" name="bookmark_sel[]" value="<?=$data[bookmark_id]?>"><br>
				<?}?>
				<input type="submit" value="수정">
			</form>
		<?}
		else
		{?>
			<div class="setting">
				<ul>
					<a href="settings.php?editblog=1"><li>블로그 관리</li></a><br>
					<a href="settings.php?editprofile=1"><li>프로필 편집</li></a><br>
					<a href="settings.php?editgroup=1"><li>게시판 그룹 관리</li></a><br>
					<a href="settings.php?editbookmark=1"><li>즐겨찾기 관리</li></a><br>
				</ul>
			</div>
		<?}?>
		</div>
	</div>
	<script src="//code.jquery.com/jquery-1.12.3.min.js"></script>
	<script>
		$('.setting_notice').click(function()
		{
			$('.setting_editnotice').slideToggle({
				complete : function()
				{
					if($('.setting_notice').hasClass("up"))
					{
						$('.setting_notice').removeClass("up");
						$('.setting_notice').addClass("down");
					}
					else
					{
						$('.setting_notice').removeClass("down");
						$('.setting_notice').addClass("up");
					}
				}
			});
		});
	</script>
	</body>
</html>
<?
include("../footer.php");
?>