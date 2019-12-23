<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$senddata[result] = false;
	if($sel_category == 1)
	{
		$sql = mysql_query("select * from item where item_category='$category' order by item_id desc");
		for($i = 0;$data = mysql_fetch_array($sql);$i++)
		{
			foreach($data as $key => $value)
			{
				if(is_numeric($key)) continue;
				if($key == "item_image")
				{
					$item_name = explode('.', $value);
					$senddata[$i]['item_name'] = $item_name[0];
					$senddata[$i][$key] = $value;
				}
				else $senddata[$i][$key] = $value;
			}
		}
		$senddata[item_num] = $i;
		$senddata[result] = true;
		echo json_encode($senddata);
	}
	else if($purchase_item == 1)
	{
		$senddata['hasitem'] = 0;
		$sql = mysql_query("select hasitem from user_info where id=$log_id");
		$data = mysql_fetch_array($sql);
		$items = explode(",", $data['hasitem']);
		foreach($items as $id)
		{
			if($id == $purchase_id)
			{
				$senddata['hasitem'] = 1;
				break;
			}
		}
		$senddata[result] = true;
		echo json_encode($senddata);
	}
	else if($delete_item == 1)
	{
		$sql = mysql_query("select item_image from item where item_id=$item_id");
		$data = mysql_fetch_array($sql);
		$filename = $data['item_image'];
		mysql_query("delete from item where item_id=$item_id");
		unlink("item/$category/$filename");
		echo json_encode(array('result'=>true));
	}
?>