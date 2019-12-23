<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_POST_VARS, $HTTP_SESSION_VARS));
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
			mysql_query("insert into item(item_category, item_image, item_name, item_desc, item_price, user_id) values('$category', '$filename', '$name', '$desc', $price, $log_id)");
			mysql_query("update user_info a,(select item_price, item_id from item order by item_id desc limit 1) b, (select concat(a.hasitem, ',',b.item_id) item from user_info a, (select item_id from item order by item_id desc limit 1) b where a.id=$log_id) c set a.point=a.point-b.item_price, a.hasitem=c.item where a.id=$log_id");
			print "<script>window.close()</script>";
		}
		else print "<script>alert('이미지 파일만 업로드할 수 있습니다.');history.go(-1)</script>";
	}
	else if($chk_price == 1)
	{
		$sql = mysql_query("select point from user_info where id=$log_id");
		$data = mysql_fetch_array($sql);
		$point = $price-$data[point];
		if($data[point] >= $price) $senddata = array("result" => true);
		else $senddata = array("result" => false, "need" => $point);
			
		echo json_encode($senddata);
		exit;
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
			<select name="category" id="category">
				<option value="title">타이틀
				<option value="bg">배경
				<option value="profile">프로필
			</select><br>
			이름 : <input type="name" name="name" id="item_name"><br>
			설명 : <input type="name" name="desc" id="item_desc"><br>
			가격 : <input type="number" name="price" value="100" id="item_price"><span></span><br>
			<input type="submit">
		</form>
		<script src="//<?=HOST?>/2016Web/1524023/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/2016Web/1524023/js/jquery-ui.min.js"></script>
		<script>
			$("#category").val("<?=$_GET['category']?>");
			$("#item_price").blur(function()
			{
				$.ajax({
						url:"additem.php",
						dataType:"json",
						type:"post",
						context:this,
						data:{chk_price:1,price:$(this).val()},
						success:function(result)
						{
							if(result['result'] == true)
							{
								$(this).next().html("");
								$(this).addClass("checked");
							}
							else 
							{
								$(this).next().html("<span style='color:#f00;font-weight:bold'> "+result['need']+"포인트가 더 필요합니다.</span>");
								$(this).removeClass("checked");
							}
						}
				});
			});
			$("#item_price").blur();
			$('#additem').submit(function()
			{
				if(!($('#item_price').hasClass("checked"))) event.preventDefault();
			});
			/*var regex = new RegExp("[^ \t\r\n\v\f]{5,20}");
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
			});*/
		</script>
	</body>
</html>
<?
include("footer.php");
?>