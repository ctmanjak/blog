<?
	session_start();
	include("config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	$sql = mysql_query("select point from user_info where id=$log_id");
	$data = mysql_fetch_array($sql);
	include("header.php");
?>
<html>
	<head>
		<meta charset="utf-8">
		<link type="text/css" href="style.css" rel="stylesheet">
		
	</head>
	
	<body>
		<div class="store_frame">
			<div class="store_category">
				<a href="additem.php" id="additem" style="position:absolute;right:130px;padding:5px;" target="_blank">아이템 올리기</a> <div style="position:absolute;right:5px;padding:5px;" id="point">내 포인트 : <?=$data['point']?></div>
				<ul>
					<li id="title">타이틀</li><li id="bg">배경</li><li id="profile">프로필사진</li>
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
							<input type="button" id="delete" class="hide" value="삭제">
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
				$('.store_category li#title').click();
			});
			$("#delete").click(function()
			{
				if(confirm("정말 삭제하시겠습니까?") == true)
				{
					$.ajax({
						url:"item.php",
						dataType:"json",
						type:"post",
						data:{delete_item:1,item_id:purchase_id,category:category},
						success:function(result)
						{
							location.href=location.href;
						}
					});
				}
			});
			$('.store_category li').on({mouseenter:function(event)
			{
				$(this).addClass("hover");
			},
			mouseleave:function(event)
			{
				if(!$(this).hasClass("selected")) $(this).removeClass("hover");
			}
			});
			$('.store_category li').click(function(event)
			{
				event.preventDefault();
				category = $(this).attr("id");
				$('.store_category li').removeClass("hover");
				$('.store_category li').removeClass("selected");
				$(this).addClass("selected");
				$("#additem").attr("href", "additem.php?category="+category);
				$.ajax({
					url:"item.php",
					dataType:"json",
					type:"post",
					context:this,
					data:{sel_category:1, category:$(this).attr("id")},
					success:function(result)
					{
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
				if('<?=$log_name?>' == 'ctwriter') $("#delete").removeClass("hide");
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
					data:{purchase_id:purchase_id, category:category, purchase_item:1},
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
								$("#point").text("내 포인트 : "+result['point']);
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