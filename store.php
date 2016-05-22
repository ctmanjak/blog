<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	
?>
<html>
	<head>
		<meta charset="utf-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		<script src="logout.js"></script>
	</head>
	
	<body>
	<div class="header">
			<div class="headerlink">
				<?
				if($logged == 1)
				{?>
					<ul><a href="/"><li>메인</li></a> l <a href="game/"><li>게임</li></a> l <a href="<?=$log_name?>/"><li>내 블로그</li></a> l <a href="settings.php"><li>설정</li> l <a href="#" onclick="logout(event)"><li>로그아웃</li></a></ul>
				<?}
				else
				{?>
					<ul><a href="login.php"><li>로그인</li></a> l <a href="register.php"><li>회원가입</li></a></ul>
				<?}?>
			</div>
		</div>
		<div class="store_frame">
			<div class="store_category">
				<ul>
					<a href="#" id="title"><li>타이틀</li></a><a href="#" id="bg"><li>배경</li></a><a href="#" id="profile"><li>프로필사진</li></a>
				</ul>
			</div>
			<div class="store_item">
			
			</div>
		</div>
		<div class="purchase_frame hide">
			<div class="purchase_window">
				<div class="purchase_image">
					<img src="">
				</div>
				<div class="purchase_info">
					<div class="purchase_id hide"></div>
					<div class="purchase_name"></div>
					<div class="purchase_desc"></div>
					<div class="purchase_price"></div>
					<?if($logged == 1)
					{?>
						<div class="purchase_button">
							<input type="button" id="purchase" value="구입">
						</div>
					<?}?>
				</div>
			</div>
		</div>
		<script src="//<?=HOST?>/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/js/jquery-ui.min.js"></script>
		<script>
			var category;
			$(document).ready(function()
			{
				$.ajax({
					url:"item.php",
					dataType:"json",
					type:"post",
					data:{sel_category:1, category:"title"},
					success:function(result)
					{
						category = "title";
						if(result['result'] == true)
						{
							for(var i=0;i < result['item_num'];i++)
								$(".store_item").append("<div class='item' id='"+result[i]['item_id']+"'><div class='item_image'><img id='image' src='./item/"+result[i]['item_category']+"/"+result[i]['item_image']+"'></div><div class='item_info'><div class='item_name' title='"+result[i]['item_name']+"'>"+result[i]['item_name']+"</div><div class='item_desc hide' title='"+result[i]['item_desc']+"'>"+result[i]['item_desc']+"</div><div class='item_price'>point : "+result[i]['item_price']+"</div></div></div>");
						}
					}
				});
			});
			$('.store_category a').click(function(event)
			{
				event.preventDefault();
				console.log("category");
				$.ajax({
					url:"item.php",
					dataType:"json",
					type:"post",
					context:this,
					data:{sel_category:1, category:$(this).attr("id")},
					success:function(result)
					{
						category = $(this).attr("id");
						if(result['result'] == true)
						{
							$(".store_item").html("");
							for(var i=0;i < result['item_num'];i++)
								$(".store_item").append("<div class='item' id='"+result[i]['item_id']+"'><div class='item_image'><img id='image' src='./item/"+result[i]['item_category']+"/"+result[i]['item_image']+"'></div><div class='item_info'><div class='item_name' title='"+result[i]['item_name']+"'>"+result[i]['item_name']+"</div><div class='item_desc hide' title='"+result[i]['item_desc']+"'>"+result[i]['item_desc']+"</div><div class='item_price'>point : "+result[i]['item_price']+"</div></div></div>");
						}
					}
				});
			});
			var purchase_id, purchase_price;
			$('body').on('click', '.item', function(event)
			{
				$(".purchase_frame").removeClass("hide");
				purchase_id = $(this).attr("id");
				$(".purchase_image > img").attr('src', $("#image", this).attr('src'));
				$(".purchase_name").html($(".item_name", this).text());
				$(".purchase_desc").html($(".item_desc", this).text());
				$(".purchase_price").html($(".item_price", this).text());
				$.ajax({
					url:"item.php",
					dataType:"json",
					type:"post",
					data:{purchase_id:purchase_id, category:category},
					success:function(result)
					{
						if(result['result'] == true)
						{
							if(result['hasitem'] == 1)
							{
								$("#purchase").attr("disabled", "disabled");
								$("#purchase").attr("value", "소유중");
							}
							else
							{
								$("#purchase").removeAttr("disabled");
								$("#purchase").attr("value", "구입");
							}
						}
					}
				});
			});
			$('#purchase').click(function(event)
			{
				$.ajax({
					url:"purchase.php",
					dataType:"json",
					type:"post",
					context:this,
					data:{purchased_item:purchase_id, purchase_price:$(".purchase_price").text(), category:category},
					success:function(result)
					{
						if(result['result'] == true)
						{
							if(result['error_type'] == 1)
							{
								alert("포인트가 부족합니다.");
							}
							else
							{
								alert("구입이 완료되었습니다.");
							}
						}
						$(".purchase_frame").addClass("hide");
					}
				});
			});
			$('.purchase_frame').click(function(event)
			{
				$(".purchase_frame").addClass("hide");
			});
			$('.purchase_window').click(function(event)
			{
				event.stopPropagation();
			});
		</script>
	</body>
</html>
<?
include("footer.php");
?>