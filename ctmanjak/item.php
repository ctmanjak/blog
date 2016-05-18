<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$senddata[result] = false;
	if($sel_category == 1)
	{
		$json_items = file_get_contents("item.json");
		$items = json_decode($json_items, true);
		$cmd="";
		$i = 0;
		if(!empty($items[$category]))
		{
			foreach($items[$category] as $id)
			{
				if(!empty($cmd)) $cmd .= " or item_id=$id";
				else $cmd .= "item_id=$id";
			}
			$sql = mysql_query("select * from item where item_category='$category' and ".$cmd." order by item_id desc");
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
		$senddata[equipped] = 0;
		if($equip_id != 0)
		{
			$sql = mysql_query("select item_image from item where item_id=$equip_id");
			$data = mysql_fetch_array($sql);
			$ext = pathinfo($data[item_image], PATHINFO_EXTENSION);
			$filename = "../item/".$category."/".$item_image;
			if(file_exists($category."_".$equip_id.".".$ext)) $senddata[equipped] = 1;
		}
		else
		{
			if(file_exists("def".$category.".png")) $senddata[equipped] = 1;
		}
		$senddata[result] = true;
		echo json_encode($senddata);
	}
?>