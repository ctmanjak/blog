<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	define("NT_ADDBM", 0x1);
	define("NT_NEWPOST", 0x2);
	if($uploadpost == 1)
	{
		$post_content=strip_tags($post_content);
		$post_content=htmlspecialchars($post_content);
		$tmp = getdate();
		$date = "$tmp[year]-$tmp[mon]-$tmp[mday] $tmp[hours]:$tmp[minutes]:$tmp[seconds]";
		$sql = mysql_query("select post_id, user_info.post_num from user_info, board, board_name where user_info.id=board.user_id and board.user_id=board_name.user_id and board_name.user_id='$owner_id' and board.board_id=board_name.board_id order by post_id desc limit 1");
		$data = mysql_fetch_array($sql);
		$post_id = $data[post_id] + 1;
		$user_post_num = $data[post_num] + 1;
		$sql = mysql_query("select * from board, board_name where board.user_id=board_name.user_id and board_name.user_id='$owner_id' and board.board_id=board_name.board_id and board_name.board_id=$board_id order by post_id desc limit 1");
		$data = mysql_fetch_array($sql);
		$post_num = $data[post_num] + 1;
		mysql_query("insert into board(post_id, user_id, board_id, post_name, post_content, post_date) values('$post_id', '$owner_id', '$board_id', '$post_name', '$post_content', '$date')");
		mysql_query("update board_name set post_num='$post_num' where user_id='$owner_id' and board_id='$board_id'");
		mysql_query("update user_info set post_num='$user_post_num' where id='$owner_id'");
		$sql = mysql_query("select user_id from bookmark where bookmark_id=$owner_id");
		while($data = mysql_fetch_array($sql))
		{
			$sql2=mysql_query("select notice_id from notice where get_user_id=$data[user_id]");
			$data2=mysql_fetch_array($sql2);
			$notice_id = $data2[notice_id]+1;
			mysql_query("insert into notice(notice_id, get_user_id, send_user_id, notice_type, notice_data) values($notice_id, $data[user_id], $owner_id, ".NT_NEWPOST.", $post_id)");
		}
		print "<meta http-equiv='refresh' content='0;url=board.php?readpost=1&post_id=$post_id'>";
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
	</head>
	<body>
		<form method="post" action="newpost.php">	
		<input type="hidden" name="uploadpost" value="1">
			게시판 : <select name="board_id">
			<?
				$sql = mysql_query("select board_id, board_name from board_name where user_id='$owner_id'");
				while($data = mysql_fetch_array($sql))
				{
					if($data[board_id] == $board_id) print "<option value='$data[board_id]' selected>$data[board_name]</option>";
					else print "<option value='$data[board_id]'>$data[board_name]</option>";
				}
			?></select><br>
			제목 : <input type="text" name="post_name" style="width:94.6%" autocomplete="off"><p>
			<textarea name="post_content" style="width:100%;height:70%"></textarea><p>
			<div align="right"><input type="submit" value="확인"></div>
		</form>
	</body>
</html>