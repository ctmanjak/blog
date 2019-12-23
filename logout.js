var logout = function(event)
{
	event.preventDefault();
	$.ajax({
		url:"http://<?=HOST?>/2016Web/logout.php",
		dataType:"json",
		type:"post",
		success:function(result)
		{
			location.href="../";
		}
	});
};