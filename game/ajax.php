<?
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$senddata=array("result" => false);
	$sql = mysql_query("select username from user where id=$log_id");
	$data = mysql_fetch_array($sql);
	$log_name = $data[username];
	if(!empty($chk_haschar) && $chk_haschar==1)
	{
		$sql = mysql_query("select haschar from user_info where id=$log_id");
		$data = mysql_fetch_array($sql);
		$exp_table = file_get_contents("exp_table.json");
		$lvlup_exp = json_decode($exp_table, true);
		if($data[haschar] == 1)
		{
			$file_player = file_get_contents("../$log_name/player.json");
			$player = json_decode($file_player, true);
			$senddata['player'] = $player;
		}
		$senddata['haschar'] = $data[haschar];
		$senddata['result'] = true;
		//mysql_query("update user_info set haschar=1 where id=$log_id");
	}
	else if(!empty($save) && $save == 1)
	{
		$player = json_encode($save_data);
		file_put_contents("../".$log_name."/player.json", $player, FILE_USE_INCLUDE_PATH);
		$senddata['result'] = true;
		$senddata['player'] = json_decode($player);
	}
	else if(!empty($load) && $load == 1)
	{
		$file_player = file_get_contents("../$log_name/player.json");
		$player = json_decode($file_player);
		$senddata['player'] = $player;
	}
	echo json_encode($senddata);
?>