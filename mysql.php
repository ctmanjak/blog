<?
	session_start();
	include("./config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	function getOwner($username, $cmd="*")
	{
		$sql = mysql_query("select user.id, nickname, blog_title from user left join user_info on user.id=user_info.id where username='$username'");
		$data = mysql_fetch_array($sql);
		$owner_id = $data[id];
		$owner_nick = $data[nickname];
		$owner_title = $data[blog_title];
		$returndata[owner_id] = $owner_id;
		$returndata[owner_nick] = $owner_nick;
		$returndata[owner_title] = $owner_title;
		$sql = mysql_query("select ".$cmd." from user left join user_info on user.id=user_info.id where user_info.id=$owner_id");
		$data = mysql_fetch_array($sql);
		foreach($data as $key => $value)
		{
			if(!is_numeric($key)) $returndata[$key] = $value;
		}
		
		return $returndata;
	}
	function getBoard($id, $cmd="*")
	{
		$sql = mysql_query("select ".$cmd." from board_name where user_id='$id'");
		while($data = mysql_fetch_array($sql))
		{
			$board[$data[board_id]] = $data[board_name];
		}
		$returndata[board]=$board;
		return $returndata;
	}
	function getLoguser($id, $cmd="*")
	{
		$sql = mysql_query("select user.id, username, nickname from user left join user_info on user.id=user_info.id where user_info.id=$id");
		$data = mysql_fetch_array($sql);
		$log_nick = $data[nickname];
		$log_name = $data[username];
		$returndata[log_nick] = $log_nick;
		$returndata[log_name] = $log_name;
		$sql = mysql_query("select ".$cmd." from user left join user_info on user.id=user_info.id where user_info.id=$id");
		$data = mysql_fetch_array($sql);
		foreach($data as $key => $value)
		{
			if(!is_numeric($key)) $returndata[$key] = $value;
		}
		
		return $returndata;
	}
	function getBookmark($id, $cmd="*")
	{
		$sql = mysql_query("select ".$cmd." from bookmark where user_id='$id'");
		while($data = mysql_fetch_array($sql))
		{
			$bookmark[$data2[username]] = array("bookmark_id"=>$data[bookmark_id], "bookmark_name"=>$data[bookmark_name]);
		}
		$returndata[bookmark]=$bookmark;
		
		return $returndata;
	}
	function getNotice($id, $cmd="*")
	{
		$sql=mysql_query("select ".$cmd." from notice where get_user_id=$id");
		$data=mysql_fetch_array($sql);
		foreach($data as $key => $value)
		{
			if(!is_numeric($key)) $returndata[$key] = $value;
		}
		
		return $returndata;
	}
	function getData($table, $terms, $cmd="*")
	{
		$sql=mysql_query("select ".$cmd." from ".$table." where ".$terms);
		for($i=0; $data=mysql_fetch_array($sql); $i++)
		{
			foreach($data as $key => $value)
			{
				if(!is_numeric($key)) $returndata[$i][$key] = $value;
			}
		}
		
		return $returndata;
	}
	function function_name($cmd="*")
	{
		$sql=mysql_query("select ".$cmd." from table where ");
		$data=mysql_fetch_array($sql);
		foreach($data as $key => $value)
		{
			if(!is_numeric($key)) $returndata[$key] = $value;
		}
		
		return $returndata;
	}
?>