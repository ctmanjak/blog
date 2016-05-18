<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	if($deletepost == 2)
	{
		$sql = mysql_query("select * from user, board, board_name where board.board_id=board_name.board_id and user.id=board.user_id and board.user_id=board_name.user_id and board_name.user_id='$owner_id' and post_id='$post_id'");
		$sql2 = mysql_query("select post_num from user_info where id='$owner_id'");
		$data = mysql_fetch_array($sql);
		$data2 = mysql_fetch_array($sql2);
		if($data[pwd] != $user_pwd)
		{
			print "<script>alert('비밀번호가 틀립니다.');</script>";
			$deletepost = 1;
			print "<script>history.go(-2)</script>";
		}
		else
		{
			$post_num = $data[post_num] - 1;
			$user_post_num = $data2[post_num] - 1;
			mysql_query("update board_name set post_num=$post_num where user_id='$owner_id' and board_id='$data[board_id]'");
			mysql_query("update user_info set post_num=$user_post_num where id='$owner_id'");
			mysql_query("delete from board where user_id='$owner_id' AND post_id='$post_id'");
			print "삭제되었습니다.";
			print "<script>setTimeout(function(){history.go(-3);}, 1000)</script>";
		}
	}
	else if($modifypost == 2)
	{
		$post_content=strip_tags($post_content);
		$post_content=htmlspecialchars($post_content);
		mysql_query("update board set board_id='$board_id', post_name='$post_name', post_content='$post_content' where user_id='$owner_id' AND post_id='$post_id'");
		print "수정되었습니다.";
		print "<script>setTimeout(function(){history.go(-2);}, 1000)</script>";
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
	</head>
	<body>
	<?
	if($deletepost == 1)
	{?>
		<form method="post" action="modifypost.php">
			<input type="hidden" name="deletepost" value="2">
			<input type="hidden" name="post_id" value="<?=$post_id?>">
			삭제하시려면 비밀번호를 입력해주세요.<p>
			<input type="password" name="user_pwd">
			<input type="submit" value="삭제">
		</form>
	<?}
	else if($modifypost == 1)
	{?>
		<form method="post" action="modifypost.php">	
		<input type="hidden" name="modifypost" value="2">
		<input type="hidden" name="post_id" value="<?=$post_id?>">
			게시판 : <select name="board_id">
			<?
				$sql = mysql_query("select board_id, board_name from board_name where user_id='$owner_id'");
				$sql2 = mysql_query("select post_name, post_content, b.board_id, board_name from board as b left join board_name as bn on b.board_id=bn.board_id where b.user_id='$owner_id' AND post_id='$post_id'");
				$data2 = mysql_fetch_array($sql2);
				while($data = mysql_fetch_array($sql))
				{
					if($data[board_id] == $data2[board_id]) print "<option selected value=$data2[board_id]>$data[board_name]";
					else print "<option value=$data2[board_id]>$data[board_name]";
				}
			?></select><br>
			<?
			?>
			제목 : <input type="text" name="post_name" style="width:94.6%" value="<?=$data2[post_name]?>" autocomplete="off"><p>
			<textarea name="post_content" style="width:100%;height:70%"><?=$data2[post_content]?></textarea><p>
			<div align="right"><input type="submit" value="수정"></div>
		</form>
	<?}?>
	</body>
</html>