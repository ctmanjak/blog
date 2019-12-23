<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	if($equip_id != 0)
	{
		$sql = mysql_query("select item_image from item where item_id=$equip_id");
		$data = mysql_fetch_array($sql);
		mysql_query("update user_info set ".$category."pic='$data[item_image]' where id='$log_id'");
	}
	else
	{
		mysql_query("update user_info set ".$category."pic='def".$category.".png' where id='$log_id'");
	}
	$senddata[result]=true;
	echo json_encode($senddata);
?>