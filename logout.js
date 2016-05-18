var logout = function(event)
{
	event.preventDefault();
	$.ajax({
		url:"/logout.php",
		dataType:"json",
		type:"post",
		success:function(result)
		{
			location.href=location.href;
		}
	});
};