<?
	include("config.cfg");
    header("Content-type: application/json");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	session_register('logged','log_id');
    
	$user_name = htmlspecialchars($user_name);
	$user_name = mysql_escape_string($user_name);
	if($login == 1)
	{
		$sql = mysql_query("select id,pwd,username from user where username='$user_name'");		
		$data = mysql_fetch_array($sql);
		if(!empty($data))
		{
			if($data[pwd] == substr(md5($user_pwd), 0, 20))
			{
				$logged=1;
				$log_id=$data[id];
				$_SESSION['log_name']=$data[username];
				echo json_encode(array('error' => 0));
			}
			else echo json_encode(array('error' => 2));
		}
		else {
            echo json_encode(array('error' => 1));
        }
	}
	mysql_close();
?>