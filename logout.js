var logout = function(event)
{
	event.preventDefault();
	$.ajax({
		url:"http://<?=HOST?>/blog/logout.php",
		dataType:"json",
		type:"post",
		success:function(result)
		{
			location.href="../";
		}
	});
};