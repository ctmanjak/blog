<?
	include("config.cfg");
	session_start();
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$sql = mysql_query("select * from user left join user_info on user.id=user_info.id where user.id='$log_id'");
	$data = mysql_fetch_array($sql);
	if(empty($viewpost_num)) $viewpost_num=0;
	$search=strip_tags($search);
	$search=htmlspecialchars($search);
?>
<html>
	<head>
		<title><?=$search?> l 검색</title>
		<meta charset="UTF-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
		<div class="header">
			<div class="headerlink">
				<ul><a href="./"><li>메인</li></a> l 
				<?
					if($logged == 1)
					{?>
						<a href="<?=$log_name?>/"><li>내 블로그</li></a> l <a href="settings.php"><li>설정</li> l <a href="index.php?logout=1"><li>로그아웃</li></a>
					<?}
					else
					{?>
						<a href="login.php"><li>로그인</li></a> l <a href="register.php"><li>회원가입</li></a>
					<?}?>
					</ul>
			</div>
			<a href="./"><div class="headerlogo"></div></a>
			<div class="hsearchbar">
				<form method="get" action="search.php">
					<input type="hidden" name="category" value="<?=ALL?>">
					<input type="text" name="search" value="<?=$search?>" autocomplete="off"><input type="submit" value="검색">
				</form>
			</div>
			
		</div>
			<div class="frame_category">
				<ul>
					<a href="search.php?category=<?=ALL?>&search=<?=$search?>"><li id=<?=ALL?> class="category">전체</li></a><a href="search.php?category=<?=POST?>&search=<?=$search?>"><li id=<?=POST?> class="category">게시글</li></a><a href="search.php?category=<?=USER?>&search=<?=$search?>"><li id=<?=USER?>  class="category">유저</li></a>
				</ul>
			</div>

		<div class="search">
			<?
			$limit_start = $viewpost_num*SEARCH_LIMIT;
			if($category&POST)
			{
				if($category == ALL) print "<span style='font-weight:bold;'>게시글</span><p></p>";
				if(!empty($search))
				{
					$search = preg_replace("/\s+/", " ", $search);
					$search_array = explode(" ", $search);
					foreach($search_array as $word)
					{
						if(!empty($word))
						{
							if($category == ALL)
							{
								$sql = mysql_query("select count(*) post_num from user_info left join board on user_info.id=board.user_id where post_name like '%$word%' or post_content like '%$word%' order by post_id desc");
								$data = mysql_fetch_array($sql);
								$post_num = $data[post_num];
								$sql = mysql_query("select * from user_info left join board on user_info.id=board.user_id where post_name like '%$word%' or post_content like '%$word%' order by post_id desc limit 5");
							}
							else
							{
								$sql = mysql_query("select count(*) post_num from user_info left join board on user_info.id=board.user_id where post_name like '%$word%' or post_content like '%$word%' order by post_id desc");
								$data = mysql_fetch_array($sql);
								$post_num = $data[post_num];
								$sql = mysql_query("select * from user_info left join board on user_info.id=board.user_id where post_name like '%$word%' or post_content like '%$word%' order by post_id desc limit $limit_start, ".SEARCH_LIMIT);
							}
							while($data = mysql_fetch_array($sql))
							{
								if(!@in_array($data[post_id], $result[$data[id]]))
								{
									$result[$data[id]][] = $data[post_id];
								}
							}
						}
					}
					if(!empty($result))
					{
						foreach($result as $user_id => $posts)
						{
							foreach($posts as $post_id)
							{
								$sql = mysql_query("select * from user, user_info, board where user.id=user_info.id and user_info.id=board.user_id and board.user_id='$user_id' and post_id=$post_id");
								$data = mysql_fetch_array($sql);?>
								<a href="./<?=$data[username]?>?post_id=<?=$data[post_id]?>"><?=$data[post_name]." - ".$data[blog_title]?></a><br>
								<div class="searchcontent"><?=$data[post_content]?></div><br>
							<?}
							if($category == ALL && $post_num > 5) print "<div style='font-weight:bold;color:blue' align='right'><a href='search.php?category=".POST."&search=$search'>더 보기</a></div>";
						}
					}
					else print "'$search' 에 대한 검색결과가 없습니다.";
				}
				else print "'$search' 에 대한 검색결과가 없습니다.";
				for($i = 0, $j=$i+1; $category != ALL && $post_num > $i*SEARCH_LIMIT; $j++, $i++)
				{
					if(($viewpost_num>=$post_num_limit-4 ? $i >= $viewpost_num-(9-($post_num_limit-$viewpost_num-1)) : $i >= $viewpost_num-5) && ($viewpost_num<5 ? $i <= $viewpost_num+(9-$viewpost_num) : $i <= $viewpost_num+4))
					{
						if($viewpost_num == $i) print "<b> $j</b>";
						else print "<div style='display:inline'><a style='display:inline' href='search.php?viewpost_num=$i&category=".POST."&search=$search'> $j</a></div>";
					}
				}
			}
			
			unset($result);
			if($category&USER)
			{
				if($category == ALL)
				{
					print "<div class='searchline'></div><br>";
					print "<span style='font-weight:bold;'>유저</span><p></p>";
				}
				if(!empty($search))
				{
					$search = preg_replace("/\s+/", " ", $search);
					$search_array = explode(" ", $search);
					foreach($search_array as $word)
					{
						if(!empty($word))
						{
							if($category == ALL)
							{
								$sql = mysql_query("select * from user_info left join board on user_info.id=board.user_id where nickname like '%$word%' group by user_info.id order by post_id desc limit 5");
							}
							else
							{
								$sql = mysql_query("select * from user_info left join board on user_info.id=board.user_id where nickname like '%$word%' order by post_id desc");
							}
							while($data = mysql_fetch_array($sql))
							{
								if(!@in_array($data[id], $result))
								{
									$result[] = $data[id];
								}
							}
						}
					}
					if(!empty($result))
					{
						foreach($result as $user_id)
						{
							$sql = mysql_query("select * from user, user_info where user.id=user_info.id and user_info.id='$user_id'");
							$data = mysql_fetch_array($sql);?>
							<a href="./<?=$data[username]?>"><?=$data[nickname]." - ".$data[blog_title]?></a><br><br>
						<?}
						if($category == ALL && $post_num > 5) print "<div style='font-weight:bold;color:blue' align='right'><a href='search.php?category=".POST."&search=$search'>더 보기</a></div>";
					}
					else print "'$search' 에 대한 검색결과가 없습니다.";
				}
				else print "'$search' 에 대한 검색결과가 없습니다.";
				for($i = 0, $j=$i+1; $category != ALL && $post_num > $i*SEARCH_LIMIT; $j++, $i++)
				{
					if(($viewpost_num>=$post_num_limit-4 ? $i >= $viewpost_num-(9-($post_num_limit-$viewpost_num-1)) : $i >= $viewpost_num-5) && ($viewpost_num<5 ? $i <= $viewpost_num+(9-$viewpost_num) : $i <= $viewpost_num+4))
					{
						if($viewpost_num == $i) print "<b> $j</b>";
						else print "<div style='display:inline'><a style='display:inline' href='search.php?viewpost_num=$i&category=".USER."&search=$search'> $j</a></div>";
					}
				}
			}?>
		</div>
		<script src="//code.jquery.com/jquery-1.12.3.min.js"></script>
		<script>
			$('#<?=$category?>').addClass('active');
		</script>
	</body>
</html>
<?
include("footer.php");
?>