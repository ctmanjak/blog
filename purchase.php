<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$sql = mysql_query("select point, hasitem from user_info where id=$log_id");
	$data = mysql_fetch_array($sql);
	$point = $data[point];
	$purchase_price = explode(" : ", $purchase_price);
	$purchase_price = intval($purchase_price[1]);
	$senddata['result'] = false;
	if($purchase_price > $point)
	{
		$senddata['result'] = true;
		$senddata['error_type'] = 1;
	}
	else
	{
		$hasitem = explode(",",$data['hasitem']);
		$hasitem[] = $purchased_item;
		$user_item = implode(",",$hasitem);
		$senddata['result'] = true;
		$senddata['error_type'] = 0;
		$point -= $purchase_price;
		$senddata['point'] = $point;
		mysql_query("update user_info set point=$point, hasitem='$user_item' where id=$log_id");
	}
	echo json_encode($senddata);
?>