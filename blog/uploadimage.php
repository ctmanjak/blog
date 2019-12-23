<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	if($uploadimage == 1)
	{
		$filename = $image_name;
		$filename = iconv('utf-8', 'euckr', $filename);
		$image = base64_decode($image);
		if(!file_exists("./image/".$filename))
		{
			$dest = "./image/".$filename;
			file_put_contents($dest, $image);
		}
		else
		{
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$filename2 = basename($filename, ".".$ext);
			for($i = 1; file_exists("./image/".$filename); $i++)
			{
				$filename = $filename2."_".$i.".".$ext;
			}
			$dest = "./image/".$filename;
			file_put_contents($dest, $image);
		}
		$img = getimagesize($dest);
	}
	echo json_encode(array('result'=> true, 'image_name'=>$filename, 'width' => $img[0], 'height' => $img[1]));
?>