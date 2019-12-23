<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$senddata[result] = false;
	if($sel_category == 1)
	{
		$sql = mysql_query("select hasitem from user_info where id=$log_id");
		$data = mysql_fetch_array($sql);
		$items = explode(",",$data['hasitem']);
		$cmd="";
		$i = 0;
		if(!empty($items))
		{
			foreach($items as $id)
			{
				if(!empty($cmd)) $cmd .= " or item_id=$id";
				else $cmd .= "item_id=$id";
			}
			$sql = mysql_query("select * from item where item_category='$category' and (".$cmd.") order by item_id desc");
			for(;$data = mysql_fetch_array($sql);$i++)
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
		}
		$senddata[$i++]['item_id'] = "0";
		$senddata[item_num] = $i;
		$senddata[result] = true;
		echo json_encode($senddata);
	}
	else
	{
		if($equip_id != 0)
		{
			$sql = mysql_query("select item_image from item where item_id=$equip_id");
			$data = mysql_fetch_array($sql);
			$item_image = $data[item_image];
			$sql = mysql_query("select ".$category."pic as equip_image from user_info where id=$log_id");
			$data = mysql_fetch_array($sql);
			if($data[equip_image] == $item_image) $senddata[equipped] = 1;
			else $senddata[equipped] = 0;
		}
		else
		{
			$sql = mysql_query("select ".$category."pic as equip_image from user_info where id=$log_id");
			$data = mysql_fetch_array($sql);
			if($data[equip_image] == "def".$category.".png") $senddata[equipped] = 1;
			else $senddata[equipped] = 0;
		}
		$senddata[result] = true;
		echo json_encode($senddata);
	}
?>