<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	if($category == "title")
	{
		
				
		$sql = mysql_query("select headpic from user_info where id='$owner_id'");
		$data = mysql_fetch_array($sql);
		unlink($data[headpic]);
		$sql = mysql_query("select item_image from item where item_id=$equip_id and item_category='$category'");
		$data = mysql_fetch_array($sql);
		$item_image = $data[item_image];
		$source = "../item/".$category."/".$item_image;
		$ext = pathinfo($item_image, PATHINFO_EXTENSION);
		if($equip_id != 0)
		{
			$headpic = $category."_".$equip_id.".".$ext;
			copy($source, $headpic);
		}
		else
		{
			$headpic = "deftitle.png";
			copy("deftitle.png.bak", $headpic);
		}
		mysql_query("update user_info set headpic='$headpic' where id='$owner_id'");
	}
	else if($category == "bg")
	{
		$sql = mysql_query("select bgpic from user_info where id='$owner_id'");
		$data = mysql_fetch_array($sql);
		unlink($data[bgpic]);
		$sql = mysql_query("select item_image from item where item_id=$equip_id and item_category='$category'");
		$data = mysql_fetch_array($sql);
		$item_image = $data[item_image];
		$source = "../item/".$category."/".$item_image;
		$ext = pathinfo($item_image, PATHINFO_EXTENSION);
		if($equip_id != 0)
		{
			$bgpic = $category."_".$equip_id.".".$ext;
			copy($source, $bgpic);
		}
		else
		{
			$bgpic = "defbg.png";
			copy("defbg.png.bak", $bgpic);
		}
		mysql_query("update user_info set bgpic='$bgpic' where id='$owner_id'");
	}
	else if($category == "profile")
	{
		$sql = mysql_query("select profilepic from user_info where id='$owner_id'");
		$data = mysql_fetch_array($sql);
		unlink($data[profilepic]);
		$sql = mysql_query("select item_image from item where item_id=$equip_id and item_category='$category'");
		$data = mysql_fetch_array($sql);
		$item_image = $data[item_image];
		$source = "../item/".$category."/".$item_image;
		$ext = pathinfo($item_image, PATHINFO_EXTENSION);
		if($equip_id != 0)
		{
			$profilepic = $category."_".$equip_id.".".$ext;
			copy($source, $profilepic);
		}
		else
		{
			$profilepic = "defprofile.png";
			copy("defprofile.png.bak", $profilepic);
		}
		mysql_query("update user_info set profilepic='$profilepic' where id='$owner_id'");
	}
	$senddata[result]=true;
	echo json_encode($senddata);
?>