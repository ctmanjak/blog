<?
	session_start();
	include("../config.cfg");
	extract(array_merge($HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_SESSION_VARS));
	mysql_connect(HOST, "user", "");
	mysql_select_db("blog");
	if(!empty($logged))
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
	}
	else if(empty($logged) || $logged == 0)
	{
		echo "<script>alert('로그인 해주세요');location.href='../login.php'</script>";
	}
?>
<html>
	<head>
		<title>게임</title>
		<meta charset="utf-8">
		<link type="text/css" href="style.css" rel="stylesheet">
	</head>
	<body>
		<style id="movebg">
			@keyframes movebg
			{
				from{background-position:0px 0px;}
				to{background-position:-500px 0px;}
			}
		</style>
		<div class="startgame">
			<input type="button" id="startgamebt" value="게임시작">
		</div>
		<div class="frame">
			<div class="my_info">
				<div class="my_info_name">
					<span><?=$log_name?></span>
					<input type="button" id="save" value="저장">
					<input type="button" id="load" value="불러오기">
				</div>
				<div class="my_info_lvlxp">레벨 : <span id="level">0</span> 경험치 : <span id="exp_level">0</span>/<span id="lvlup_exp">0</span></div>
				<div class="my_info_stat">
					<div class="my_stat">힘 : <span id="stat_str">0</span><br>민첩 : <span id="stat_agi">0</span><br>지능 : <span id="stat_int">0</span><br>인내 : <span id="stat_end">0</span></div>
					<div class="inc_stat hide"><input type="button" id="stat_str" value="+"><br><input type="button" id="stat_agi" value="+"><br><input type="button" id="stat_int" value="+"><br><input type="button" id="stat_end" value="+"></div>
					SP : <span id="sp">0</span><br>
					체력 : <span id="hp">0</span><br>공격력 : <span id="ad">0</span><br>공격속도 : <span id="as">0</span><br>
					방어력 : <span id="armor">0</span><br>마법저항력 : <span id="resist">0</span><br>돈 : <span id="money">0</span>
				</div>
			</div>
			<div class="npc_info">
				<div class="npc_info_name">
					<span id="npc_name">?</span>
				</div>
				<div class="npc_info_lvlxp">레벨 : <span id="level">?</span></div>	
				<div class="npc_info_stat">
					<div class="my_stat">힘 : <span id="stat_str">?</span><br>민첩 : <span id="stat_agi">?</span><br>지능 : <span id="stat_int">?</span><br>인내 : <span id="stat_end">?</span></div><br>
					체력 : <span id="hp">?</span><br>공격력 : <span id="ad">?</span><br>공격속도 : <span id="as">?</span><br>
					방어력 : <span id="armor">?</span><br>마법저항력 : <span id="resist">?</span>
				</div>
			</div>
			<div class="gamescreen">
				<div class="background">
					<div class="bgimg"></div>
				</div>
				<div class="playerscreen">
					<img class="playerimg" src="pizzaGuyNoPizza.png">
				</div>
			</div>
			<div class="user_control">
				<div class="category">
					<ul>
						<a href="#" id="controlbox"><li class="active">명령</li></a><a href="#" id="inventory"><li>인벤토리</li></a>
					</ul>
				</div>
				<div class="controlscreen">
					<div class="controlbox">
						<input type="button" id="explore" value="탐험">
						<input type="button" id="attack" value="공격">
					</div>
					<div class="inventory hide">
						hi;
					</div>
				</div>
			</div>
		</div>
		<script src="//code.jquery.com/jquery-1.12.3.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="character.js"></script>
		<script>
			$(".category li").hover(function(event)
			{
				$(this).css("font-weight", "bold");
			},function()
			{
				if(!$(this).hasClass("active")) $(this).css("font-weight", "normal");
			});
			$(".category li").click(function(event)
			{
				event.preventDefault();
				$(".category li").css("font-weight", "normal");
				$(".category li").removeClass("active");
				$(this).css("font-weight", "bold");
				$(this).addClass("active");
				$(".controlscreen").children().addClass("hide");
				$("."+$(this).parent().attr("id")).removeClass("hide");
			});
			var state = {idle:0, encounter:1, combat:2, explore:3};
			var player = new Player();
			var npc = new Npc();
			var reload = function(target, stat)
			{
				var stat = stat || 'all';
				if(stat == 'all')
				{
					if(target == 'player')
					{
						for(var name in player)
						{
							if(!player.hasOwnProperty(name)) continue;
							$(".my_info #"+name).text(player[name]);
						}
					}
					else if(target == 'npc')
					{
						for(var name in npc)
						{
							if(!npc.hasOwnProperty(name)) continue;
							$(".npc_info #"+name).text(npc[name]);
						}
					}
				}
				else
				{
					if(target == 'player') $(".my_info #"+stat).text(player[stat]);
					else if(target == 'npc') $(".npc_info #"+stat).text(npc[stat]);
				}
			}
			$('#attack').click(function(event)
			{
				event.preventDefault();
				if(player.cur_state == state['encounter'])
				{
					if(confirm("really?") == 1) player.cur_state = state['combat'];
				}
				else if(player.cur_state == state['combat'])
				{
					npc.hp -= player.ad;
					reload("npc", "hp");
					if(npc.hp <= 0)
					{
						player.gainExp(30);
						player.cur_state = state['idle'];
						for(var name in npc)
						{
							$(".npc_info #"+name).text('?');
						}
					}
				}
			});
			$('#explore').click(function()
			{
				if(player.cur_state == state['idle'])
				{
					player.cur_state = state['explore'];
					$('.bgimg').addClass("ani_bg");
					$('.playerimg').addClass("ani_plwalk");
					setTimeout(function()
					{
						var bg_pos_string = $('.bgimg').css("background-position");
						var bg_pos = bg_pos_string.split("px");
						$('.bgimg').removeClass("ani_bg");
						$('.playerimg').removeClass("ani_plwalk");
						$('.bgimg').css("background-position", bg_pos_string);
						if(parseInt(bg_pos[0])<-500)
						{
							var pos_x =parseInt(bg_pos[0])+500;
							$('#movebg').html("@keyframes movebg{0%{background-position:"+pos_x+"px}100%{background-position:"+bg_pos[0]+"px;}}");
						}
						else
						{
							var pos_x =parseInt(bg_pos[0])-500;
							$('#movebg').html("@keyframes movebg{0%{background-position:"+bg_pos[0]+"px}100%{background-position:"+pos_x+"px;}}");
						}
						var a = Math.floor(Math.random()+1);
						if(a == 1)
						{
							npc.createRandom(player.level);
							player.cur_state = state['encounter'];
							reload("npc");
						}
					}, Math.floor(Math.random()*5000+1000));
				}
			});
			$('.inc_stat > input[type=button]').click(function(event)
			{
				event.preventDefault();
				player[$(this).attr('id')] += 1;
				player.sp--;
				if($(this).attr('id') == "stat_str")
				{
					player.ad += 1;
					player.carry_weight += 1;
					reload("player", "ad");
					reload("player", "carry_weight");
				}
				else if($(this).attr('id') == "stat_agi")
				{
					player.as += 1;
					player.ms += 1;
					reload("player", "as");
					reload("player", "ms");
				}
				else if($(this).attr('id') == "stat_int")
				{
					player.mp += 1;
					reload("player", "mp");
				}
				else
				{
					player.hp += 2;
					reload("player", "hp");
				}
				if(player.sp <= 0) $('.inc_stat').addClass("hide");
				
				reload("player", $(this).attr('id'));
				reload("player", "sp");
			});
			$('#startgamebt').click(function()
			{
				event.preventDefault();
			/*$(document).ready(function()
			{*/
				$.ajax({
					url:"ajax.php",
					dataType:"json",
					type:"post",
					data:{"chk_haschar":1, "log_id":<?=$log_id?>},
					success:function(result)
					{
						var player_data = {};
						if(result['haschar'] == 0)
						{
							player.createPlayer();
							//$("#save").click();
						}
						else
						{
							player = result['player'];
							for(var data in result['player'])
							{
								player[data] = parseInt(result['player'][data]);
							}
						}
						$(".startgame").addClass("hide");
						reload("player");
						if(player.sp > 0) $(".inc_stat").removeClass("hide");
					}
				});
			});
			$('#save').click(function(event)
			{
				event.preventDefault();
				var save_data = {};
				for(var data in player)
				{
					if(!player.hasOwnProperty(data)) continue;
					save_data[data] = player[data];
				}
				$.ajax({
					url:"ajax.php",
					dataType:"json",
					type:"post",
					data:{"save":1, "log_id":<?=$log_id?>, "save_data":save_data},
					success:function(result)
					{
						
					}
				});
			});
			$('#load').click(function(event)
			{
				event.preventDefault();
				$.ajax({
					url:"ajax.php",
					dataType:"json",
					type:"post",
					data:{"load":1, "log_id":<?=$log_id?>},
					success:function(result)
					{
						for(var data in result['player'])
						{
							player[data] = parseInt(result['player'][data]);
						}
					}
				});
			});
			$(window).on('beforeunload', function(event)
			{
				//$('#save').click();
			});
		</script>
	</body>
</html>
<?
include("../footer.php");
?>