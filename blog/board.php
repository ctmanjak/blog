<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where username='$owner'");
	$data = mysql_fetch_array($sql);
	$head_color = $data[head_color];
	$info_color = $data[info_color];
	$board_color = $data[board_color];
	$boardn_color = $data[boardn_color];
	if(empty($board_id)) $board_id=0;
	if(empty($viewpost_num)) $viewpost_num=0;
	if($addcomment == 1)
	{
		$com_content=strip_tags($com_content);
		$com_content=htmlspecialchars($com_content);
		$sql = mysql_query("select comment_num from board where post_id=$post_id and user_id=$owner_id");
		$data = mysql_fetch_array($sql);
		$comment_num = $data[comment_num]+1;
		$tmp = getdate();
		$date = "$tmp[year]-$tmp[mon]-$tmp[mday] $tmp[hours]:$tmp[minutes]:$tmp[seconds]";
		mysql_query("insert into comment(post_id, user_id, com_user_id, comment_id, com_content, com_date) values($post_id, $owner_id, $log_id, $comment_id, '$com_content', '$date')");
		$sql2=mysql_query("select notice_id from notice where get_user_id=$owner_id");
		$data2=mysql_fetch_array($sql2);
		$notice_id = $data2[notice_id]+1;
		$notice_data = implode(",", array($post_id, $comment_id));
		if($log_id != $owner_id) mysql_query("insert into notice(notice_id, get_user_id, send_user_id, notice_type, notice_data) values($notice_id, $owner_id, $log_id, ".NT_NEWCOMMENT.", '$notice_data')");
		mysql_query("update board set comment_num=$comment_num where post_id=$post_id and user_id=$owner_id");
		print "<meta http-equiv='refresh' content='0; url=board.php?readpost=1&post_id=$post_id'>";
	}
	else if($editcomment == 2)
	{
		$com_content=strip_tags($com_content);
		$com_content=htmlspecialchars($com_content);
		mysql_query("update comment set com_content='$com_content' where comment_id='$comment_id'");
		print "<meta http-equiv='refresh' content='0; url=board.php?readpost=1&post_id=$post_id'>";
	}
	else if($delcomment == 1)
	{
		$sql = mysql_query("select comment_num from board where post_id=$post_id and user_id=$owner_id");
		$data = mysql_fetch_array($sql);
		$comment_num = $data[comment_num]-1;
		mysql_query("delete from comment where comment_id='$comment_id'");
		mysql_query("update board set comment_num=$comment_num where post_id=$post_id and user_id=$owner_id");
		print "<meta http-equiv='refresh' content='0; url=board.php?readpost=1&post_id=$post_id'>";
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
	<div class="board_main hide">
	<?
	if($readpost == 1)
	{?>
		<table class="board_table">
		<?
		$sql = mysql_query("select * from user_info as u, board_name as bn, board as b where u.id=bn.user_id and bn.user_id=b.user_id and b.user_id='$owner_id' and bn.board_id=b.board_id and b.post_id='$post_id'");
		$data = mysql_fetch_array($sql);
		if($log_id != $owner_id)
		{
			$view_num = $data[view_num] + 1;
			mysql_query("update board set view_num='$view_num' where user_id='$owner_id' AND post_id='$post_id'");
		}
		?>
		<tr><td colspan=2 width="900px" class="post_name"><b><?=$data[post_name]?></b> l <a href='board.php?board_id=<?=$data[board_id]?>'><?=$data[board_name]?></a></td></tr>
		<tr><td colspan=4 class="board_line"></td></tr>
		<tr><td width="50%"><span style="font-size:12px"><?=$data[nickname]?></td><td align="right"><?=$data[post_date]?></td></span></tr>
		<tr><td colspan=2 class="post_content"><?
			$data[post_content] = preg_replace("/(<[^?]?[^>]*[^?\\<]*[^\\?]>)/", "\n$1\n", $data[post_content]);
			echo nl2br($data[post_content])?></td></tr>
		<tr><td colspan=4 class="board_line"></td></tr>
		</table>
		<?
		if(empty($viewcom_num)) $viewcom_num = 0;
		$limit_start = $viewcom_num*SEARCH_LIMIT;?>
		<div class="comment">
			<?
			$sql = mysql_query("select count(*) comment_num from comment where post_id=$post_id");
			$data = mysql_fetch_array($sql);
			$comment_num = $data['comment_num'];
			$sql = mysql_query("select com_user_id, username, nickname, comment_id, com_content, com_date from user, user_info, comment where user.id=user_info.id and user_info.id=comment.com_user_id and post_id=$post_id and user_id=$owner_id order by comment_id desc limit $limit_start, ".SEARCH_LIMIT);
			$tmp = getdate();
			while($data = mysql_fetch_array($sql))
			{?>
				<div class="comment_main">
					<div class="comment_header">
						<a href="../blog/?owner=<?=$data[username]?>" target="_parent"><?=$data[nickname]?></a>
						<div class="comment_option" align="right">
							<ul>
								<?if($data[com_user_id] == $log_id)
								{?>
									<a href="board.php?readpost=1&editcomment=1&comment_id=<?=$data[comment_id]?>&post_id=<?=$post_id?>"><li>수정</li></a> l <a href="board.php?delcomment=1&comment_id=<?=$data[comment_id]?>&post_id=<?=$post_id?>"><li>삭제</li></a>
								<?}?>
							</ul>
						</div>
						<div class="comment_date">
							<?
								intval($tmp[mon])<10?$mon="0".$tmp[mon]:$mon=$tmp[mon];
								$tmp2 = explode(" ",$data[com_date]);
								if($tmp2[0] == $tmp[year]."-". $mon ."-".$tmp[mday])
								{
									$date = $tmp2[1];
									echo substr($date, 0, 5);
								}
								else
								{
									$date = $data[com_date];
									echo substr($date, 0, 16);
								}
							?>
						</div>
					</div>
					<div class="comment_content">
						<?=$data[com_content]?>
					</div>
					<?if($editcomment ==1)
						{
							if($comment_id == $data[comment_id])
							{?>
								<div class="comment_text">
									<form method="post" action="board.php">
										<input type="hidden" name="editcomment" value="2">
										<input type="hidden" name="post_id" value="<?=$post_id?>">
										<input type="hidden" name="comment_id" value="<?=$comment_id?>">
										<textarea name="com_content"><?=$data[com_content]?></textarea>
										<div align="right"><input type="submit" value="수정" ></div>
									</form>
								</div>
							<?}
						}?>
					</div>
			<?}?>
		</div>
		<center>
		<?
		for($i = 0, $j=$i+1; $comment_num > $i*SEARCH_LIMIT; $j++, $i++)
		{
			if(($viewcom_num>=$post_num_limit-4 ? $i >= $viewcom_num-(9-($post_num_limit-$viewcom_num-1)) : $i >= $viewcom_num-5) && ($viewcom_num<5 ? $i <= $viewcom_num+(9-$viewcom_num) : $i <= $viewcom_num+4))
			{
				if($viewcom_num == $i) print "<b> $j</b>";
				else print "<div style='display:inline'><a style='display:inline' href='board.php?owner=$owner&readpost=1&post_id=$post_id&viewcom_num=$i'> $j</a></div>";
			}
		}?></center>
		<?
		$sql = mysql_query("select comment_id from comment where post_id=$post_id and user_id=$owner_id order by comment_id desc limit 1");
		$data = mysql_fetch_array($sql);
		if(empty($data)) $comment_id = 1;
		else $comment_id = $data[comment_id] + 1;
		?>
		<div class="comment_text">
			<form method="post" action="board.php">
				<input type="hidden" name="addcomment" value="1">
				<input type="hidden" name="post_id" value="<?=$post_id?>">
				<input type="hidden" name="comment_id" value="<?=$comment_id?>">
				<textarea name="com_content"></textarea>
				<div align="right"><input type="submit" value="등록" ></div>
			</form>
		</div>
		<?if($owner_id == $log_id) 
		{?>
			<div align="right" style="margin-top:10px;margin-right:10px"><a href="modifypost.php?modifypost=1&post_id=<?=$post_id?>">수정 </a>  l  <a href="modifypost.php?deletepost=1&post_id=<?=$post_id?>"	>삭제</a></div>
		<?}
}
	else
	{?>
		<table class="board_table">
		<tr><th width="100px" align="center"></th><th width="600px" align="center">제목</th><th width="200px" align="center">작성일</th><th width="100px" align="center">조회수</th></tr>
		<tr><td colspan=4 class="board_line"></td></tr>
		<?
		$limit_start = ($viewpost_num)*POST_LIMIT;
		if(!empty($search)) $extracmd = " and (post_name like '%$search%' or post_content like '%$search%')";
		else $extracmd = "";
		if($board_id == 0) $sql = mysql_query("select * from board where user_id='$owner_id'".$extracmd." order by post_id desc limit $limit_start, ".POST_LIMIT);
		else $sql = mysql_query("select * from board where user_id='$owner_id' AND board_id='$board_id'".$extracmd." order by post_id desc limit $limit_start, ".POST_LIMIT);
		$tmp = getdate();
		while($data = mysql_fetch_array($sql))
		{?>
			<tr><td align="center"><?=$data[post_id]?></td><td><a href="board.php?readpost=1&post_id=<?=$data[post_id]?>"><?=$data[post_name]?><?if(!empty($data[comment_num]))print "<span style='font-size:0.8em;color:#ff4500;font-weight:bold'> [$data[comment_num]]</span>";?></a></td><td align="center"><?
				intval($tmp[mon])<10?$mon="0".$tmp[mon]:$mon=$tmp[mon];
				$tmp2 = explode(" ",$data[post_date]);
				if($tmp2[0] == $tmp[year]."-". $mon ."-".$tmp[mday])
				{
					$date = $tmp2[1];
					echo substr($date, 0, 5);
				}
				else
				{
					echo $tmp2[0];
				}
				
				?></td><td align="center"><?=$data[view_num]?></td></tr>
			<tr><td colspan=4 class="board_line"></td></tr>
		<?}?>
		</table>
		<div class="bsearchbar">
			<form method="get" action="board.php">
				<input type="hidden" name="board_id" value="<?=$board_id?>">
				<input type="text" style="border:1px solid #a9a9a9" name="search" value="<?=$search?>" autocomplete="off"><input type="submit" value="검색">
			</form>
		</div>
		<center><div class="board_num">
			<?
			if($board_id == 0) $sql = mysql_query("select post_num from user_info where id='$owner_id'");
			else $sql = mysql_query("select post_num from board_name where user_id='$owner_id' and board_id='$board_id'");
			$data = mysql_fetch_array($sql);
			$post_num = $data[post_num];
			$post_num_limit = $post_num/POST_LIMIT;
			?><span style="font-size:0.95em">
			<?for($i = 0, $j=$i+1;$post_num > $i*POST_LIMIT; $j++, $i++)
			{
				if(($viewpost_num>=$post_num_limit-4 ? $i >= $viewpost_num-(9-($post_num_limit-$viewpost_num-1)) : $i >= $viewpost_num-5) && ($viewpost_num<5 ? $i <= $viewpost_num+(9-$viewpost_num) : $i <= $viewpost_num+4))
				{
					if($viewpost_num == $i) print "<b> $j</b>";
					else print "<a href='board.php?viewpost_num=$i&board_id=$board_id'> $j</a>";
				}
			}
			?>
			</span>
		</div></center>
		<?
		if($log_id == $owner_id)
		{?>
			<div align="right" style="margin-right:10px"><a href="newpost.php?board_id=<?=$board_id?>">글쓰기</a></div>
	<?}?>
<?}?>
<div class="board_bg" style="z-index:-1;top:0;position:absolute;width:100%;height:100%;"></div>
</div>
		<script src="//<?=HOST?>/2016Web/1524023/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/2016Web/1524023/js/jquery-ui.min.js"></script>
		<script>
			$('.comment_main').hover(function(event)
			{
				$('.comment_option', this).attr("style", "visibility:visible")
				$('.comment_option', this).fadeIn(200);
			},function(event)
			{
				$('.comment_option', this).fadeOut(200);
			});
		</script>
	</body>
</html>