<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	$post_id=explode("post_id=", $url);
	$sql = mysql_query("select board_id from board where post_id=$post_id[1]");
	$data = mysql_fetch_array($sql);
	echo json_encode(array('result'=>true,'board_id' => $data['board_id']));
?>