<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	
	include("../header.php");
?>
<html>
	<head>
		<meta charset="utf-8">
		<link type="text/css" href="inventory.css" rel="stylesheet">
		<script src="../logout.js"></script>
	</head>
	
	<body>
		<div class="store_frame">
			<div class="store_category">
				<ul>
					<li id="title">타이틀</li><li id="bg">배경</li><li id="profile">프로필사진</li>
				</ul>
			</div>
			<div class="store_item">
			
			</div>
		</div>
		<div class="equip_frame hide">
			<div class="equip_window">
				<div class="equip_image">
					<img src="">
				</div>
				<div class="equip_info">
					<div class="equip_id hide"></div>
					<div class="equip_name"></div>
					<div class="equip_desc"></div>
					<?if($logged == 1)
					{?>
						<div class="equip_button">
							<input type="button" class="hide" id="equip" value="적용">
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
				$('.store_category li').removeClass("hover");
				$('.store_category li').removeClass("selected");
				$(this).addClass("selected");
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
							{
								if(result[i]['item_id'] == 0) $(".store_item").append("<div class='item' id='0'><div class='item_image'><img id='image' src=''></div><div class='item_info'><div class='item_name' title='기본'>기본</div><div class='item_desc hide' title='기본'>기본</div></div></div>");
								else $(".store_item").append("<div class='item' id='"+result[i]['item_id']+"'><div class='item_image'><img id='image' src='../item/"+result[i]['item_category']+"/"+result[i]['item_image']+"'></div><div class='item_info'><div class='item_name' title='"+result[i]['item_name']+"'>"+result[i]['item_name']+"</div><div class='item_desc hide' title='"+result[i]['item_desc']+"'>"+result[i]['item_desc']+"</div></div></div>");
							}
						}
					}
				});
			});
			var equip_id;
			$('body').on('click', '.item', function(event)
			{
				$("#equip").addClass("hide");
				$(".equip_frame").removeClass("hide");
				equip_id = $(this).attr("id");
				$(".equip_image > img").attr('src', $("#image", this).attr('src'));
				$(".equip_name").html($(".item_name", this).text());
				$(".equip_desc").html($(".item_desc", this).text());
				$.ajax({
					url:"item.php",
					dataType:"json",
					type:"post",
					data:{equip_id:equip_id, category:category},
					success:function(result)
					{
						if(result['result'] == true)
						{
							if(result['equipped'] == 1)
							{
								$("#equip").attr("disabled", "disabled");
								$("#equip").attr("value", "적용됨");
							}
							else
							{
								$("#equip").removeAttr("disabled");
								$("#equip").attr("value", "적용");
							}
							$("#equip").removeClass("hide");
						}
					}
				});
			});
			$('#equip').click(function(event)
			{
				$.ajax({
					url:"equip.php",
					dataType:"json",
					type:"post",
					context:this,
					data:{equip_id:equip_id, category:category},
					success:function(result)
					{
						if(result['result'] == true)
						{
							alert("적용되었습니다.");
						}
						$(".equip_frame").addClass("hide");
					}
				});
			});
			$('.equip_frame').click(function(event)
			{
				$(".equip_frame").addClass("hide");
			});
			$('.equip_window').click(function(event)
			{
				event.stopPropagation();
			});
		</script>
	</body>
</html>
<?
include("../footer.php");
?>