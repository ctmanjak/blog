<?
	session_start();
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	$logged = 0;
	unset($log_id);
	echo json_encode(array("result" => true));
?>