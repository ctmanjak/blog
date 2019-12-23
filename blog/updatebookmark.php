<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	//owner
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user_info.id=$owner_id");
	$data = mysql_fetch_array($sql);
	foreach($data as $key => $value)	if(!is_numeric($key)) $returndata[$key] = $value;
	extract($returndata);
	//bookmark
	$sql = mysql_query("select * from bookmark where user_id='$log_id' order by bookmark_num desc");
	while($data = mysql_fetch_array($sql))
	{
		$sql2 = mysql_query("select username from user where id=$data[bookmark_id]");
		$data2 = mysql_fetch_array($sql2);
		$bookmark[$data2[username]] = $data[bookmark_name];
	}
	if($inbookmark == 1)
	{
		mysql_query("delete from bookmark where user_id=$log_id and bookmark_id=$bookmark_id");
		echo json_encode(array('result'=>true, 'type'=>2));
	}
	else
	{
		$sql = mysql_query("select bookmark_num from bookmark where user_id='$log_id' order by bookmark_num desc limit 1");
		$data = mysql_fetch_array($sql);
		if(!empty($data)) $bookmark_num = $data[bookmark_num]+1;
		else $bookmark_num = 1;
		$bookmark_name = "$nickname l $blog_title";
		mysql_query("insert into bookmark values('$log_id', '$bookmark_num', '$bookmark_id', '$bookmark_name')");
		$sql=mysql_query("select count(*) as notice_count from notice where get_user_id=$id");
		$data=mysql_fetch_array($sql);
		extract($data);
		$notice_id = $notice_count + 1;
		mysql_query("insert into notice(notice_id, get_user_id, send_user_id, notice_type) values($notice_id, $owner_id, $log_id, ".NT_ADDBM.")");
		echo json_encode(array('result'=>true, 'type'=>1));
	}
?>