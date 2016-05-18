<?
	session_start();
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	$checkbookmark == 1 ? $bookmarkbar = 1:$bookmarkbar = 0;
?>