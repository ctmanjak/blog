<?
	session_start();
	set_time_limit(0);
	include("./config.cfg");
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	if($notice_click == 1)
	{
		$sql = mysql_query("select send_user_id, notice_type, notice_id, notice_data, nickname from notice, user_info where id=get_user_id and get_user_id=$log_id order by notice_id desc");
		$senddata = array('result'=>true);
		while($data = mysql_fetch_array($sql))
		{
			$sql2 = mysql_query("select nickname, username from user, user_info where user.id=user_info.id and user_info.id=$data[send_user_id]");
			$data2 = mysql_fetch_array($sql2);
			if(empty($data))
			{
				$senddata[result]=false;
				break;
			}
			$notice = array('send_user_nick'=>$data2[nickname], 'send_user'=>$data2[username], 'notice_type'=>$data[notice_type], 'notice_id'=>$data[notice_id], 'notice_data'=>$data[notice_data]);
			array_push($senddata, $notice);
		}
		if($senddata[result]) mysql_query("update notice set is_read=1 where get_user_id=$log_id");
		echo json_encode($senddata);
		mysql_query("delete from notice where is_read=1");
	}
	else if($check_notice == 1)
	{
		clearstatcache();
		$sql=mysql_query("select count(*) as notice_count from notice where get_user_id=$log_id");
		$data=mysql_fetch_array($sql);
		if($data['notice_count'] != $cur_count)
		{
			$senddata['notice_count'] = $data['notice_count'];
			echo json_encode($senddata);
			break;
		}
		else
		{
			sleep(1);
			continue;
		}
	}
?>