<?
	session_start();
	include $_SERVER["DOCUMENT_ROOT"]."/config.cfg";
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	session_register('bookmarkbar');
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$notice_count = 0;
	if(empty($bookmarkbar)) $bookmarkbar = 0;
	if($logged == 1)
	{
		//log_id
		$sql = mysql_query("select user.id, username, nickname from user left join user_info on user.id=user_info.id where user_info.id=$log_id");
		$data = mysql_fetch_array($sql);
		$log_nick = $data[nickname];
		$log_name = $data[username];
		$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user_info.id=$log_id");
		$data = mysql_fetch_array($sql);
		foreach($data as $key => $value) if(!is_numeric($key)) $returndata[$key] = $value;
		extract($returndata);
		//bookmark
		$sql = mysql_query("select * from bookmark where user_id='$log_id'");
		while($data = mysql_fetch_array($sql))
		{
			$sql2 = mysql_query("select username from user where id=$data[bookmark_id]");
			$data2 = mysql_fetch_array($sql2);
			$bookmark[$data2[username]] = $data[bookmark_name];
		}
		//notice
		$sql=mysql_query("select count(*) as notice_count from notice where get_user_id=$log_id");
		$data=mysql_fetch_array($sql);
		if(!empty($data)) extract($data);
	}
?>
<html>
	<head>
		<meta charset="UTF-8"/>
		<link type="text/css" href="http://<?=HOST?>/footer.css" rel="stylesheet">
	</head>
	<body>
		<?if($logged == 1)
			{?>
				<div class="noticebt">
					<span><?=$notice_count?></span>
				</div>
				<div class="notice_frame hide">
					<table>
						
					</table>
				</div>
				<div class="bookmark"><span>즐겨찾기</span><span style="position:absolute;text-align:right;font-size:10px;top:2px;right:2px;"><a href="#" onclick="closebookmark(event)">닫기</a></span><br>
					<select id="bookmarksel">
						<option>즐겨찾기 목록
						<?
						if(!empty($bookmark))
						{
							foreach($bookmark as $url => $name)
							{
								print "<option value='../$url'>$name";
							}
						}?>
					</select>
				</div>
				<div class="openbookmark hide">
					<a href="#" onclick="openbookmark(event)">즐겨찾기</a>
				</div>
		<?}?>
		<script src="//<?=HOST?>/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/js/jquery-ui.min.js"></script>
		<script>
			$(document).ready(function()
			{
				if(<?=$notice_count?> != 0) $('.noticebt').css("background-color", "#ff4500");
				if(<?=$bookmarkbar?> == 0) closebookmark();
			});
			
			
			$("#bookmarksel").on("change", function(event)
			{
				parent.location.href = $("#bookmarksel").val();
			});
			$(".noticebt").click(function()
			{
				if($(".notice_frame").hasClass("hide"))
				{
					$(".notice_frame").toggle('slide', {direction : 'down'}, 200);
					$(".notice_frame").removeClass("hide");
					$('.noticebt').css("border-radius", "0px");
					$('.noticebt').css("background-color", "#add8e6");
					$.ajax({
					url : '../notice.php',
					dataType:'json',
					type:'POST',
					data:{user_id:<?=$log_id?>},
					success:function(result)
					{
						if(result['result'] == true)
						{
							$('.notice_frame').html("<table></table>");
							for(var i = 0; i < <?=$notice_count?>;i++)
							{
								if(result[i]['notice_type'] == <?=NT_ADDBM?>) $('.notice_frame > table').append("<tr><td><a href='../"+result[i]['send_user']+"'>"+result[i]['send_user_nick']+"</a>님이 <?=$log_name?>님을 즐겨찾기에 등록했습니다.<br></td></tr>");
								else if(result[i]['notice_type'] == <?=NT_NEWPOST?>) $('.notice_frame > table').append("<tr><td><a href='../"+result[i]['send_user']+"/?post_id="+result[i]['notice_data']+"'>"+result[i]['send_user_nick']+"님이 새로운 글을 등록했습니다.<br></a></td></tr>");
								else if(result[i]['notice_type'] == <?=NT_NEWCOMMENT?>)
								{
									$data = result[i]['notice_data'].split(",");
									$('.notice_frame > table').append("<tr><td><a href='../<?=$log_name?>/?post_id="+$data[0]+"'><?=$log_name?>님의 글에 새로운 덧글이 등록되었습니다.<br></a></td></tr>");
								}
							}
							$('.noticebt').load("index.php .noticebt>*");
							$('.noticebt').css("border-radius", "0px");
						}
					}
					});
					$('.noticebt').css("border-radius", "0px");
				}
				else
				{
					$(".notice_frame").toggle('slide', {direction : 'down'}, 200, function()
						{
							$('.noticebt').css("border-radius", "5px");
						});
					
					$(".notice_frame").addClass("hide");
				}
			});
			var closebookmark = function(event)
			{
				$.ajax({
					url:"/checkbookmark.php",
					dataType:"json",
					type:"post",
					data:{checkbookmark:0}
				});
				$(".bookmark").addClass("hide");
				$(".openbookmark").removeClass("hide");
				if(event) event.preventDefault();
			}
			var openbookmark = function(event)
			{
				$.ajax({
					url:"/checkbookmark.php",
					dataType:"json",
					type:"post",
					data:{checkbookmark:1}
				});
				$(".openbookmark").addClass("hide");
				$(".bookmark").removeClass("hide");
				if(event) event.preventDefault();
			}
		</script>
	</body>
</html>