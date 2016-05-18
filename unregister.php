<?
	include("config.cfg");
	session_start();
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	if($unregister == 1)
	{
		$sql = mysql_query("select * from user where id='$log_id'");
		$data = mysql_fetch_array($sql);
		if($data[pwd] != $user_pwd)
		{
			print "<script>alert('비밀번호가 틀립니다.');</script>";
		}
		else
		{
			mysql_query("delete from user where id=$data[id]");
			mysql_query("delete from user_info where id=$data[id]");
			mysql_query("delete from board_name where user_id=$data[id]");
			mysql_query("delete from board where user_id=$data[id]");
			mysql_query("delete from bookmark where user_id=$data[id]");
			mysql_query("delete from comment where user_id=$data[id]");
			$dir = "./".$data[username];
			$files = scandir($dir);
			foreach($files as $file)
			{
				if(!is_dir($file)) unlink($dir."/".$file);
			}
			rmdir($data[username]);
			print "<meta http-equiv='refresh' content='0; url=index.php?logout=1'>";
		}
	}
	
	mysql_close();
?>
<html>
	<head>
		<meta charset="UTF-8">
	</head>
	<body>
		<form method="post" action="unregister.php">
			<input type="hidden" name="unregister" value="1">
			탈퇴하시려면 비밀번호를 입력해주세요.<br>
			탈퇴하시면 블로그의 모든 게시글이 삭제되며 다시 복구할 수 없습니다.<br>
			다른 사람의 블로그에 쓴 댓글은 삭제되지 않습니다.<p>
			<input type="password" name="user_pwd">
			<input type="submit" value="탈퇴하기" onclick="">
		</form>
	</body>
</html>