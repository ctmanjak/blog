<?
	session_start();
	include("../config.cfg");
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
		<div class="front">
		</div>
		<div class="frame">
			<div class="player_info_frame">
				<div class="player_info">
					<div class="player_info_name">
						<span id="char_name">?</span>
					</div>
					<center>
					<input type="button" id="save" value="저장">
					<input type="button" id="load" value="불러오기">
					</center>
					<div class="player_info_lvlxp">레벨 : <span id="level">0</span> 경험치 : <span id="exp_level">0</span>/<span id="lvlup_exp">0</span></div>
					<div class="player_info_stat">
						<div class="player_stat">힘 : <span id="stat_str">0</span><br>민첩 : <span id="stat_agi">0</span><br>지능 : <span id="stat_int">0</span><br>인내 : <span id="stat_end">0</span></div>
						<div class="inc_stat hide"><input type="button" id="stat_str" value="+"><br><input type="button" id="stat_agi" value="+"><br><input type="button" id="stat_int" value="+"><br><input type="button" id="stat_end" value="+"></div>
						SP : <span id="sp">0</span><br>
						HP : <span id="hp">0</span> / <span id="max_hp">0</span><br>MP : <span id="mp">0</span> / <span id="max_mp">0</span><br>공격력 : <span id="ad">0</span><br>공격속도 : <span id="as">0</span><br>
						방어력 : <span id="armor">0</span><br>마법저항력 : <span id="resist">0</span><br>돈 : <span id="money">0</span>
					</div>
				</div>
			</div>
			<div class="player_list">
				<ul>
				
				</ul>
			</div>
			<div class="player_effect">
				<ul>
				
				</ul>
			</div>
			<div class="npc_info_frame">
				<div class="npc_info">
					<ul>
						
					</ul>
					<div class="npc_info_name">
						<span id="char_name">?</span>
					</div>
					<div class="npc_info_lvlxp">레벨 : <span id="level">?</span></div>	
					<div class="npc_info_stat">
						<div class="npc_stat">힘 : <span id="stat_str">?</span><br>민첩 : <span id="stat_agi">?</span><br>지능 : <span id="stat_int">?</span><br>인내 : <span id="stat_end">?</span></div><br>
						HP : <span id="hp">?</span> / <span id="max_hp">?</span><br>MP : <span id="mp">?</span> / <span id="max_mp">?</span><br>공격력 : <span id="ad">?</span><br>공격속도 : <span id="as">?</span><br>
						방어력 : <span id="armor">?</span><br>마법저항력 : <span id="resist">?</span>
					</div>
				</div>
			</div>
			<div class="npc_list">
				<ul>
					
				</ul>
			</div>
			<div class="gamescreen">
				<div class="location_info">
					<div class="location_name">
					</div>
				</div>
				<div class="background">
					<div class="bgimg"></div>
				</div>
				<div class="dialogue_frame">
					<div class="dialogue_text">
						<div class="dialogue_name">
						</div>
						<div calss="dialogue_content">
						</div>
					</div>
				</div>
				<!--<div class="playerscreen">
					<div class="playerimg"></div>
				</div>
				<div class="npcscreen">
					<div class="npcimg"></div>
				</div>-->
			</div>
			<div class="user_control">
				<div class="category">
					<ul>
						<a href="#" id="controlbox"><li class="active">명령</li></a><a href="#" id="inventory"><li>인벤토리</li></a>
					</ul>
				</div>
				<div class="controlscreen">
					<div class="controlbox">
						<input type="button" id="test" value="TEST">
						<input type="button" id="explore" value="탐험">
						<input type="button" id="attack" value="공격">
						<input type="button" class="hide" id="move_location" value="지역 이동">
					</div>
					<div class="inventory hide">
						hi;
					</div>
				</div>
			</div>
		</div>
		<script src="//<?=HOST?>/js/jquery.min.js"></script>
		<script src="//<?=HOST?>/js/jquery-ui.min.js"></script>
		<script src="character.js"></script>
		<script src="magiceffect.js"></script>
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
			var state = {dead:0, idle:1, encounter:2, combat:3, explore:4};
			var magic_effect_id = {heal:0x1, stun:0x2, silence:0x4, binding:0x8, invisible:0x10, bleeding:0x20, resistance:0x40, debuff:0x80, buff:0x100, damage:0x200};
			var magic_effect = {heal:{id:0x1, duration:0, amount:0}};
			var heal_effect = {id:0x1, duration:0, amount:0};
			var damage_effect = {id:0x200, duration:0, amount:0};
			var personality = {good:1, neutral:2, bad:3};
			var player;
			var followers = [];
			var npcs = [];
			var active_npc = {};
			var active_player = {};
			var npcs_info = [];
			var player_location = 1;
			var npc_all_dead = 0;
			var follower_all_dead = 0;
			var combat_players = [];
			var combat_npcs = [];
			var cur_location = {};
			var cur_state = state['idle'];
			var moveLocation = function(id)
			{
				var npc_id = [];
				$.ajax({
					url:"ajax.php",
					dataType:"json",
					type:"post",
					async:false,
					data:{get_location:1, location_id:id},
					success:function(result)
					{
						if(result['result'] == true)
						{ 	
							var location_effects = result['location']['location_effect'];
							for(var i = 0; i < location_effects.length; i++)
							{
								if(location_effects[i]['id'] != 0)
								{
									for(var j = 0; j < followers.length; j++)
									{
										addMagicEffect(followers[j], location_effects[i]);
									}
									addMagicEffect(player, location_effects[i]);
								}
							}
							cur_location = result['location'];
							for(var val in result['location']['encounter_id'])
							{
								npc_id.push(result['location']['encounter_id'][val]);
							}
						}
					}
				});
					$.ajax({
					url:"ajax.php",
					dataType:"json",
					type:"post",
					async:false,
					data:{get_npc:1, npc_id:npc_id},
					success:function(result)
					{
						if(result['result'] == true)
						{
							for(var npc in result['npc'])
							{
								npcs_info.push(result['npc'][npc]);
							}
						}
					}
				});
				$(".bgimg").css("background-image", "url('bg/"+cur_location['bg']+"')");
				$(".location_name").html(cur_location['name']);
			}
			var reload = function(target, stat)
			{
				var stat = stat || 'all';
				if(stat == 'all')
				{
					for(var name in target)
					{
						if(!target.hasOwnProperty(name)) continue;
						$("."+target.type+"_info #"+name).text(target[name]);
					}
				}
				else if(stat == 'hp')
				{
					if(target.type == "player" && target != player)
					{
						for(var i = 0, j = 0; i < followers.length; i++, j=i+1)
						{
							if(followers[i] == target)
							{
								$('.'+target.type+'_list li[id='+target.type+'_'+j+']>span[id=hp]').text(target.hp);
								break;
							}
						}
					}
					else if(target.type == "npc")
					{
						for(var i = 0; i < npcs.length; i++)
						{
							if(npcs[i] == target)
							{
								$('.'+target.type+'_list li[id='+target.type+'_'+i+']>span[id=hp]').text(target.hp);
								break;
							}
						}
					}
					else
					{
						$('.'+target.type+'_list li[id='+target.type+']>span[id=hp]').text(target.hp);
					}
					$("."+target.type+"_info #"+stat).text(target[stat]);
				}
				else
				{
					$("."+target.type+"_info #"+stat).text(target[stat]);
				}
				if(target === undefined) target={};
				if(target.sp <= 0) $(".inc_stat").addClass("hide");
				else $(".inc_stat").removeClass("hide");
			}
			$("#test").click(function(event)
			{
				event.preventDefault();
				/*$(".front").addClass("ani_npc_attack");
				setTimeout(function()
				{
					$(".front").removeClass("ani_npc_attack");
				}, 300);*/
				convertType(active_npc);
			});
			$('#attack').click(function(event)
			{
				$(".frame").addClass(".ani_player_attack");
				setTimeout(function()
				{
					$(".frame").removeClass(".ani_player_attack");
				}, 500);
				event.preventDefault();
				if(cur_state == state['encounter'])
				{
					for(var i = 0; i < followers.length; i++)
					{
						combat_players.push(followers[i]);
					}
					combat_players.push(player);
					for(var i = 0; i < npcs.length; i++)
					{
						combat_npcs.push(npcs[i]);
					}
					progressCombat(combat_players, combat_npcs);
				}
				else if(cur_state == state['combat'])
				{
					$('#attack').attr("disabled", "disabled");
					setTimeout(function()
					{
						$('#attack').removeAttr("disabled");
					}, active_player.bat/((100+active_player.as)*0.01)*1000);
					adjustMagicEffect(damage_effect, {duration:1, amount:player.ad, type:"physical"});
					addMagicEffect(active_npc, damage_effect);
					reload(active_npc, 'hp');
					if(active_npc.hp <= 0)
					{
						for(var i = 0; i < combat_npcs.length; i++)
						{
							if(combat_npcs[i] === active_npc)
							{
								if(i >= combat_npcs.length-1)
								{
									npc_all_dead = 1;
								}
								//gainExpPlayer(npcs[i].exp_gain);
								gainExpPlayer(30);
								if(npc_all_dead != 1) $(".select_npc.active").next()[0].click();
								combat_npcs.splice(i, 1);
								break;
							}
						}
						if(npc_all_dead == 1)
						{
							cur_state = state['idle'];
						}
					}
				}
			});
			var gameOver = function()
			{
				if(cur_state != state['dead'])
				{
					cur_state=state['dead'];
					console.log("Game Over!!!!");
				}
			}
			var combat_npc = function(id, enemy)
			{
				var select_enemy = enemy[Math.floor(Math.random()*enemy.length)];
				if(select_enemy == player)
				{
					select_enemy = player;
				}
				for(var i = 0; i < followers.length; i++)
				{
					if(select_enemy == followers[i]) select_enemy = followers[i];
				}
				if(select_enemy !== undefined && select_enemy.hp <= 0)
				{
					if(select_enemy == player) gameOver();
					for(var i = 0; i < enemy.length; i++)
					{
						if(enemy[i] === select_enemy)
						{
							enemy.splice(i, 1);
							break;
						}
					}
					select_enemy = enemy[Math.floor(Math.random()*enemy.length)];
				}
				if(!(cur_state==state['dead']) && !(npcs[id].hp <= 0))
				{
					adjustMagicEffect(damage_effect, {duration:1, amount:npcs[id].ad, type:"physical"});
					addMagicEffect(select_enemy, damage_effect);
					reload(select_enemy, "hp");
					console.log(select_enemy);
					setTimeout(combat_npc, npcs[id].bat/((100+npcs[id].as)*0.01)*1000, id, enemy);
				}
			}
			var combat_follower = function(id, enemy)
			{
				var select_enemy = enemy[Math.floor(Math.random()*enemy.length)];
				for(var i = 0; i < npcs.length; i++)
				{
					if(select_enemy == npcs[i]) select_enemy = npcs[i];
				}
				if(select_enemy !== undefined && select_enemy.hp <= 0 && npc_all_dead != 1)
				{
					for(var i = 0; i < enemy.length; i++)
					{
						if(enemy[i] === select_enemy)
						{
							if(i >= enemy.length-1)
							{
								npc_all_dead = 1;
							}
							//gainExpPlayer(enemy[i].exp_gain);
							gainExpPlayer(30);
							enemy.splice(i, 1);
							break;
						}
					}
					select_enemy = enemy[Math.floor(Math.random()*enemy.length)];
				}
				if(!(cur_state==state['dead']) && !(followers[id].hp <= 0) && !(npc_all_dead == 1))
				{
					adjustMagicEffect(damage_effect, {duration:1, amount:followers[id].ad, type:"physical"});
					addMagicEffect(select_enemy, damage_effect);
					reload(select_enemy, "hp");
					setTimeout(combat_follower, followers[id].bat/((100+followers[id].as)*0.01)*1000, id, enemy);
				}
			}
			var progressCombat = function(combat_players, combat_npcs)
			{
				cur_state = state['combat'];
				
				for(var i = 0; i < npcs.length; i++)
				{
					npcs[i]['attack_cycle'] = setTimeout(combat_npc, npcs[i].bat/((100+npcs[i].as)*0.01)*1000, i, combat_players);
				}
				for(var i = 0; i < followers.length; i++)
				{
					followers[i]['attack_cycle'] = setTimeout(combat_follower, followers[i].bat/((100+followers[i].as)*0.01)*1000, i, combat_npcs);
				}
			}
			var gainExpPlayer = function(exp)
			{
				var increase_exp = followers.length * 0.15;
				var gain_exp = Math.floor((exp*(1+increase_exp))/(followers.length+1));
				for(var i = 0; i < followers.length; i++)
				{
					followers[i].gainExp(gain_exp);
				}
				player.gainExp(gain_exp);
			}
			$("#move_location").click(function(event)
			{
				event.preventDefault();
			});
			var img = new Image();
			$('#explore').click(function(event)
			{
				event.preventDefault();
				if(true || cur_state == state['idle'])
				{
					if(player_location++ >= 10) $("#move_location").removeClass("hide");
					cur_state = state['explore'];
					$('.bgimg').addClass("ani_bg");
					//$('.playerimg').addClass("ani_plwalk");
					setTimeout(function()
					{
						var bg_pos_string = $('.bgimg').css("background-position");
						var bg_pos = bg_pos_string.split("px");
						img.src = $(".bgimg").css("background-image").split("\"")[1];
						var bg_width = img.width*($(".bgimg").height()/img.height);
						$('.bgimg').removeClass("ani_bg");
						//$('.playerimg').removeClass("ani_plwalk");
						$('.bgimg').css("background-position", bg_pos_string);
						if(parseInt(bg_pos[0])<-500)
						{
							var pos_x =parseInt(bg_pos[0])+bg_width;
							var dest_x = pos_x-500;
							$('#movebg').html("@keyframes movebg{0%{background-position:"+pos_x+"px}100%{background-position:"+bg_pos[0]+"px;}}");
						}
						else
						{
							var pos_x =parseInt(bg_pos[0])-500;
							$('#movebg').html("@keyframes movebg{0%{background-position:"+bg_pos[0]+"px}100%{background-position:"+pos_x+"px;}}");
						}
						$.ajax({
							url:"location.json",
							dataType:"json",
							type:"post",
							success:function()
							{
								
							}
						});
						var encounter_chance = Math.floor(Math.random()*2+1);
						npcs = [];
						$('.npc_list > ul').empty();
						if(encounter_chance == 1)
						{
							createNpc(1);
							$(".select_npc")[0].click();
						}
						else if(encounter_chance == 2)
						{
							createNpc(10);
							$(".select_npc")[0].click();
						}
					}, Math.floor(Math.random()*1));
					cur_state = state['encounter'];
				}
			});
			var convertType = function(npc)
			{
				var temp = new Player();
				temp.createPlayer();
				for(var val in npc)
				{
					if(!temp.hasOwnProperty(val)) continue;
					temp[val] = npc[val];
				}
				var temp_name;
				if(confirm("정말 동료로 영입하시겠습니까?") == false)
				{
					return;
				}
				if(temp_name = prompt("동료의 별명을 입력해주세요. 입력하지 않거나 취소를 누르면 원래 이름으로 등록됩니다.") == "" || temp_name == null)
				{
					temp['char_name'] = npc['char_name'];
				}
				else temp['char_name'] = temp_name;
				temp['type'] = "player";
				temp['sp'] = 0;
				var id = followers.length;
				followers.push(temp);
				$('.select_npc.active').remove();
				if(!($(".npc_list > ul").children()[0] === undefined)) $(".npc_list > ul").children()[0].click();
				else cur_state = state['idle'];
				$('.player_list > ul').append("<a href='#' class='select_player'><li id='player_"+id+"'>"+temp['char_name']+" <span id='hp'>"+temp['hp']+"</span>/<span id='max_hp'>"+temp['max_hp']+"</span></li></a>");
			}
			var createNpc = function(num)
			{
				for(var i=0; i < num; i++)
				{
					npcs.push(new Npc());
				}
				for(var i = 0; i < npcs.length; i++)
				{
					var select_npc = npcs_info[Math.floor(Math.random()*npcs_info.length)];
					npcs[i].createRandom(select_npc);
					$('.npc_list > ul').append("<a href='#' class='select_npc'><li id='npc_"+i+"'>"+npcs[i]['char_name']+" <span id='hp'>"+npcs[i]['hp']+"</span>/<span id='max_hp'>"+npcs[i]['max_hp']+"</span></li></a>");
				}
			}
			$('body').on('click', '.select_npc', function(event)
			{
				event.preventDefault();
				$('.select_npc').removeClass("active");
				$(this).addClass("active");
				active_npc = npcs[$("li", this).attr("id").split("_")[1]];
				reload(active_npc);
			});
			$('body').on('click', '.select_player', function(event)
			{
				event.preventDefault();
				$('.select_player').removeClass("active");
				$(this).addClass("active");
				if($(this).attr("id") == "player") active_player = player;
				else active_player = followers[$("li", this).attr("id").split("_")[1]];
				reload(active_player);
			});
			$('.inc_stat > input[type=button]').click(function(event)
			{
				event.preventDefault();
				active_player[$(this).attr('id')] += 1;
				active_player.sp--;
				if($(this).attr('id') == "stat_str")
				{
					active_player.ad += 1;
					active_player.carry_weight += 1;
					reload(active_player, "ad");
					reload(active_player, "carry_weight");
				}
				else if($(this).attr('id') == "stat_agi")
				{
					active_player.as += 1;
					active_player.ms += 1;
					reload(active_player, "as");
					reload(active_player, "ms");
				}
				else if($(this).attr('id') == "stat_int")
				{
					active_player.max_mp += 1;
					reload(active_player, "max_mp");
				}
				else
				{
					active_player.max_hp += 2;
					reload(active_player, "max_hp");
				}
				
				if(active_player.sp <= 0) $('.inc_stat').addClass("hide");
				
				reload(active_player, $(this).attr('id'));
				reload(active_player, "sp");
			});
			/*$('#startgamebt').click(function()
			{
				event.preventDefault();*/
			$(document).ready(function()
			{
				$.ajax({
					url:"ajax.php",
					dataType:"json",
					type:"post",
					async:false,
					data:{"chk_haschar":1, "log_id":<?=$log_id?>},
					success:function(result)
					{
						if(result['haschar'] == 0)
						{
							player = new Player();
							player.createPlayer();
							player.char_name = "<?=$log_name?>";
							//adjustMagicEffect(heal_effect, {name:"heal", duration:100, amount:1});
							//addMagicEffect(player, heal_effect);
							//$("#save").click();
							$("#explore").click();
							$('.player_list > ul').append("<a href='#' class='select_player' id='player'><li id='player'>"+player['char_name']+" <span id='hp'>"+player['hp']+"</span>/<span id='max_hp'>"+player['max_hp']+"</span></li></a>");
						}
						else
						{
							$("#load").click();
							/*player = result['player'];
							for(var data in result['player'])
							{
								player[data] = parseInt(result['player'][data]);
							}*/
						}
						$(".startgame").addClass("hide");
						$(".select_player")[0].click();
						reload(active_player);
						//moveLocation(cur_location['id']);
						moveLocation(0);
						if(active_player.sp > 0) $(".inc_stat").removeClass("hide");
					}
				});
			});
			
			$('#save').click(function(event)
			{
				event.preventDefault();
				var save_data = {};
				if(save_data['follower'] === undefined) save_data['follower'] ={};
				for(var i= 0; i < followers.length; i++)
				{
					for(var data in followers[i])
					{
						if(!followers[i].hasOwnProperty(data)) continue;
						save_data['follower'][i][data] = followers[i][data];
					}
				}
				if(save_data['player'] === undefined) save_data['player'] ={};
				for(var data in player)
				{
					if(!player.hasOwnProperty(data)) continue;
					save_data['player'][data] = player[data];
				}
				save_data['cur_location'] = cur_location;
				save_data['cur_state'] = cur_state;
				$.ajax({
					url:"ajax.php",
					dataType:"json",
					type:"post",
					data:{"save":1, "log_id":<?=$log_id?>, "save_data":save_data},
					success:function(result)
					{
						console.log(result);
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
					async:false,
					data:{"load":1, "log_id":<?=$log_id?>},
					success:function(result)
					{
						for(var i = 0; i < result['player'].length; i++)
						{
							for(var data in result['player'][i])
							{
								if(data != "char_name" && data != "type" && data != "magic_effect" && data != "equip_slot")
									followers[i][data] = parseInt(result['player'][i][data]);
							}
						}
					cur_location = result['cur_location'];
					cur_state = result['cur_state'];
					}
				});
				reload(player);
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