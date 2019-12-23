<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	if($deletepost == 2)
	{
		$sql = mysql_query("select * from user, board, board_name where board.board_id=board_name.board_id and user.id=board.user_id and board.user_id=board_name.user_id and board_name.user_id='$owner_id' and post_id='$post_id'");
		$sql2 = mysql_query("select post_num from user_info where id='$owner_id'");
		$data = mysql_fetch_array($sql);
		$data2 = mysql_fetch_array($sql2);
		if($data[pwd] != substr(md5($user_pwd), 0, 20))
		{
			print "<script>alert('비밀번호가 틀립니다.');</script>";
			$deletepost = 1;
			print "<script>history.go(-2)</script>";
		}
		else
		{
			$post_num = $data[post_num] - 1;
			$user_post_num = $data2[post_num] - 1;
			mysql_query("update board_name set post_num=$post_num where user_id='$owner_id' and board_id='$data[board_id]'");
			mysql_query("update user_info set post_num=$user_post_num where id='$owner_id'");
			mysql_query("delete from board where user_id='$owner_id' AND post_id='$post_id'");
			mysql_query("delete from comment where post_id='$post_id'");
			print "<script>location.href='board.php'</script>";
		}
	}
	else if($modifypost == 2)
	{
		$post_content=strip_tags($post_content, "<img>");
		mysql_query("update board set board_id='$board_id', post_name='$post_name', post_content='$post_content' where user_id='$owner_id' AND post_id='$post_id'");
		print "수정되었습니다.";
		print "<script>history.go(-2)</script>";
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
	<div class="board_main">
	<?
	if($deletepost == 1)
	{?>
		<form method="post" action="modifypost.php">
			<input type="hidden" name="deletepost" value="2">
			<input type="hidden" name="post_id" value="<?=$post_id?>">
			삭제하시려면 비밀번호를 입력해주세요.<p>
			<input type="password" name="user_pwd">
			<input type="submit" value="삭제">
		</form>
	<?}
	else if($modifypost == 1)
	{?>
	<form method="post" action="modifypost.php" id="modifypost">	
		<input type="hidden" name="modifypost" value="2">
		<input type="hidden" name="post_id" value="<?=$post_id?>">
		<input type="hidden" name="post_content" id="post_content">
			게시판 : <select name="board_id">
			<?
				$sql = mysql_query("select board_id, board_name from board_name where user_id='$owner_id'");
				$sql2 = mysql_query("select post_name, post_content, b.board_id, board_name from board as b left join board_name as bn on b.board_id=bn.board_id where b.user_id='$owner_id' AND post_id='$post_id'");
				$data2 = mysql_fetch_array($sql2);
				while($data = mysql_fetch_array($sql))
				{
					if($data[board_id] == $data2[board_id]) print "<option selected value=$data2[board_id]>$data[board_name]";
					else print "<option value=$data2[board_id]>$data[board_name]";
				}
			?></select><br>
			<?
			?>
			제목 : <input type="text" name="post_name" style="width:94%;border:1px solid #a9a9a9" value="<?=$data2[post_name]?>" autocomplete="off"><p>
			<div style="width:100%;height:450px;border:1px solid #a9a9a9;overflow:auto;" id="content" contentEditable><?=$data2[post_content]?></div>
			<ul id="image_list" style="border:1px solid #a9a9a9;height:150px;overflow:auto;"></ul>
			<input type="file" id="image" accept="image/*">
			<div align="right"><input type="submit" value="수정"></div>
		</form>
	<?}?>
	<div class="board_bg" style="z-index:-1;top:0;position:absolute;width:100%;height:100%;"></div>
	</div>
	<script src="//<?=HOST?>/2016Web/1524023/js/jquery.min.js"></script>
	<script src="//<?=HOST?>/2016Web/1524023/js/jquery-ui.min.js"></script>
	<script>
		$("#modifypost").submit(function(event)
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