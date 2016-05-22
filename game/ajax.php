<?
	include("../config.cfg");
	session_start();
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
			$json_player = file_get_contents("../$log_name/player.json");
			$player = json_decode($json_player, true);
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
	}
	else if(!empty($load) && $load == 1)
	{
		$json_player = file_get_contents("../$log_name/player.json");
		$player = json_decode($json_player);
		$senddata['result'] = true;
		$senddata['player'] = $player;
	}
	else if($get_location == 1)
	{
		$json_location = file_get_contents("location.json");
		$location = json_decode($json_location);
		$senddata['result'] = true;
		$senddata['location'] = $location[$location_id];
	}
	else if($get_npc == 1)
	{
		$json_npc = file_get_contents("npc.json");
		$npc = json_decode($json_npc);
		foreach($npc_id as $id)
		{
			$senddata['npc'][] = $npc[$id];
		}
		$senddata['result'] = true;
	}
	echo json_encode($senddata);
?>