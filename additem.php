<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	if($additem == 1)
	{
		$check = @getimagesize($_FILES["image"]["tmp_name"]);
		if($check !== false) $upload = 1;
		else $upload = 0;
		if($upload == 1)
		{
			$name = strip_tags($name);
			$desc = strip_tags($desc);
			$filename = $_FILES['image']['name'];
			$filename = iconv('utf-8', 'euckr', $filename);
			if(!file_exists("./item/".$category."/".$filename))
			{
				$dest = "./item/".$category."/".$filename;
				$src = $_FILES['image']['tmp_name'];
				move_uploaded_file($src, $dest);
			}
			else
			{
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				$filename2 = basename($filename, ".".$ext);
				for($i = 1; file_exists("./item/".$category."/".$filename); $i++)
				{
					$filename = $filename2."_".$i.".".$ext;
				}
				$dest = "./item/".$category."/".$filename;
				$src = $_FILES['image']['tmp_name'];
				move_uploaded_file($src, $dest);
			}
			$filename = mysql_escape_string($filename);
			$filename = iconv('euckr', 'utf-8', $filename);
			$sql = mysql_query("select item_id from item where item_category='$category' order by item_id desc limit 1");
			$data = mysql_fetch_array($sql);
			if(!empty($data)) $item_id = $data[item_id] + 1;
			else $item_id = 1;
			mysql_query("insert into item(item_id, item_category, item_image, item_name, item_desc, item_price, user_id) values($item_id, '$category', '$filename', '$name', '$desc', $price, $log_id)");
			print "<script>history.go(-1)</script>";
		}
		else print "<script>alert('이미지 파일만 업로드할 수 있습니다.');history.go(-1)</script>";
		
	}
?>
<html>
	<head>
		<meta charset="utf-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
		<form method="post" action="additem.php" enctype="multipart/form-data" autocomplete="off" id="additem">
			<input type="hidden" name="MAX_FILE_SIZE" value="1048576">
			<input type="hidden" name="additem" value="1">
			<input type="file" name="image" accept="image/*"><br>
			<select name="category">
				<option value="title">타이틀
				<option value="bg">배경
				<option value="profile">프로필
			</select><br>
			이름 : <input type="name" name="name" id="item_name"><span></span><br>
			설명 : <input type="name" name="desc" id="item_desc"><br>
			가격 : <input type="number" name="price" value="100" id="item_price"><br>
			<input type="submit">
		</form>
		<script src="//code.jquery.com/jquery-1.12.3.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script>
			var regex = new RegExp("[^ \t\r\n\v\f]{5,20}");
			$('#additem').submit(function()
			{
				if(!($('#item_name').hasClass("checked"))) event.preventDefault();
			});
			$('#item_name').blur(function()
			{
				$.ajax({
						url:"check_regex.php",
						dataType:"json",
						type:"post",
						data:{'chk_item':1, 'item_name':$("#item_name").val()},
						success:function(result)
						{
							if(result['result'] == true)
							{
								if(result['error_type'] == 2)
								{
									$("#item_name").next().replaceWith("<span style='color:#f00;font-weight:bold'> 5~20자의 공백과 .을 제외한 문자만 사용 가능합니다.</span>");
									$("#item_name").removeClass("checked");
								}
								else if(result['error_type'] == 1)
								{
									$("#item_name").next().replaceWith("<span style='color:#f00;font-weight:bold'> 중복되는 아이템 이름이 있습니다.</span>");
									$("#item_name").removeClass("checked");
								}
								else
								{
									$("#item_name").next().replaceWith("<span style='color:#0f0;font-weight:bold'> 사용가능한 이름입니다.</span>");
									$("#item_name").addClass("checked");
								}
							}
						}
					});
			});
		</script>
	</body>
</html>
<?
include("footer.php");
?>