<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	define("NT_ADDBM", 0x1);
	define("NT_NEWPOST", 0x2);
	if($uploadpost == 1)
	{
		$post_content=strip_tags($post_content, "<img>");
		$tmp = getdate();
		$date = "$tmp[year]-$tmp[mon]-$tmp[mday] $tmp[hours]:$tmp[minutes]:$tmp[seconds]";
		$sql = mysql_query("select post_id, user_info.post_num from user_info, board, board_name where user_info.id=board.user_id and board.user_id=board_name.user_id and board_name.user_id='$owner_id' and board.board_id=board_name.board_id order by post_id desc limit 1");
		$data = mysql_fetch_array($sql);
		$post_id = $data[post_id] + 1;
		$user_post_num = $data[post_num] + 1;
		$sql = mysql_query("select * from board, board_name where board.user_id=board_name.user_id and board_name.user_id='$owner_id' and board.board_id=board_name.board_id and board_name.board_id=$board_id order by post_id desc limit 1");
		$data = mysql_fetch_array($sql);
		$post_num = $data[post_num] + 1;
		mysql_query("insert into board(post_id, user_id, board_id, post_name, post_content, post_date) values('$post_id', '$owner_id', '$board_id', '$post_name', '$post_content', '$date')");
		mysql_query("update board_name set post_num='$post_num' where user_id='$owner_id' and board_id='$board_id'");
		mysql_query("update user_info set post_num='$user_post_num' where id='$owner_id'");
		$sql = mysql_query("select user_id from bookmark where bookmark_id=$owner_id");
		while($data = mysql_fetch_array($sql))
		{
			$sql2=mysql_query("select notice_id from notice where get_user_id=$data[user_id]");
			$data2=mysql_fetch_array($sql2);
			$notice_id = $data2[notice_id]+1;
			mysql_query("insert into notice(notice_id, get_user_id, send_user_id, notice_type, notice_data) values($notice_id, $data[user_id], $owner_id, ".NT_NEWPOST.", $post_id)");
		}
		print "<meta http-equiv='refresh' content='0;url=board.php?readpost=1&post_id=$post_id'>";
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
	<div class="board_main">
		<form method="post" action="newpost.php" enctype="multipart/form-data" id="newpost">	
			<input type="hidden" name="MAX_FILE_SIZE" value="1048576">
			<input type="hidden" name="uploadpost" value="1">
			<input type="hidden" name="post_content" id="post_content">
			게시판 : <select name="board_id">
			<?
				$sql = mysql_query("select board_id, board_name from board_name where user_id='$owner_id'");
				while($data = mysql_fetch_array($sql))
				{
					if($data[board_id] == $board_id) print "<option value='$data[board_id]' selected>$data[board_name]</option>";
					else print "<option value='$data[board_id]'>$data[board_name]</option>";
				}
			?></select><br>
			제목 : <input type="text" name="post_name" id="post_name" style="width:94%;border:1px solid #a9a9a9" autocomplete="off"><p>
			<div style="width:100%;height:450px;border:1px solid #a9a9a9;overflow:auto;" id="content" contentEditable></div>
			<ul id="image_list" style="border:1px solid #a9a9a9;height:150px;overflow:auto;"></ul>
			<input type="file" id="image" accept="image/*">
			<div align="right"><input type="submit" value="확인"></div>
		</form>
		<div class="board_bg" style="z-index:-1;top:0;position:absolute;width:100%;height:100%;"></div>
	</div>
	<script src="//<?=HOST?>/2016Web/1524023/js/jquery.min.js"></script>
	<script src="//<?=HOST?>/2016Web/1524023/js/jquery-ui.min.js"></script>
	<script>
		$("#newpost").submit(function(event)
		{
			if($("#post_name").val() == "")
			{
				event.preventDefault();
				alert("제목을 입력해주세요");
			}
			else if($("#content").text() == "")
			{
				event.preventDefault();
				alert("내용을 입력해주세요");
			}
			$("#post_content").val($("#content").html());
		});
		$("#image").change(function(event)
		{
			var reader = new FileReader();
			var image_name = $(this)[0].files[0]['name'];
			reader.readAsDataURL($(this)[0].files[0]);
			reader.onload = function()
			{
				$.ajax({
					url:"uploadimage.php",
					dataType:"json",
					type:"post",
					data:{uploadimage:1,image:reader.result.split(",")[1],image_name:image_name},
					success:function(result)
					{
						$("#image_list").append("<li style='height:50px;list-style-type:none;'><img src='http://<?=HOST?>/2016Web/1524023/blog/image/"+result['image_name']+"' style='float:left;height:100%;width:auto;'><div align='right' id='setsize' style='position:absolute;right:0;z-index:1;'><input type='checkbox' id='link_size' checked><input type='number' id='width' placeholder='넓이' value='"+result['width']+"'><br><input type='number' id='height' placeholder='높이' value='"+result['height']+"'></div><div style='z-index:0;position:relative;top:50%;transform:translate(0,-50%)'> &nbsp"+result['image_name']+"</div></li>");
						$("#image").val("");
					}
				});
			}
		});
		$("body").on("click", "#setsize", function(event)
		{
			event.stopPropagation();
		});
		$("body").on("keydown", "#setsize input#width", function(event)
		{
			if(event.keyCode == 13)
			{
				event.preventDefault();
				$(this).next().next().focus();
			}
		});
		$("body").on("keydown", "#setsize input#height", function(event)
		{
			if(event.keyCode == 13)
			{
				event.preventDefault();
				$(this).prev().prev().focus();
			}
		});
		$("body").on("change", "#setsize input#width", function(event)
		{
			if($(this).prev()[0].checked == true) $(this).next().next().val(Math.round(($(this).next().next()[0].defaultValue/$(this)[0].defaultValue)*$(this).val()));
		});
		$("body").on("change", "#setsize input#height", function(event)
		{
			if($(this).prev().prev().prev()[0].checked == true) $(this).prev().prev().val(Math.round(($(this).prev().prev()[0].defaultValue/$(this)[0].defaultValue)*$(this).val()));
		});
		var select, select_content=0;
		$("#content").mousedown(function()
		{
			select_content = 1;
		});
		$(document).mouseup(function()
		{
			if(select_content == 1)
			{
				select = window.getSelection().getRangeAt(0);
				select_content = 0;
			}
		});
		$("body").on("click", "#image_list li", function()
		{
			var image = new Image();
			image.src = $(this).children('img').attr('src');
			image.width = $(this).children('div#setsize').children('input')[1].value;
			image.height = $(this).children('div#setsize').children('input')[2].value;
			if(select !== undefined)
			{
				select.deleteContents();
				select.insertNode(image);
			}
			else $("#content").append(image);
		});
	</script>
	</body>
</html>