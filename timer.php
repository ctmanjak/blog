<html>
<head>
<title> he</title>
</head>
<body>
<span id="time_out">0.00</span>
<br><br>
<button id="start_timer" onclick="start()">Start</button>
<button id="stop_timer" onclick="stop()">Stop</button>
<button id="reset_timer" onclick="reset()">Reset</button>

<script> 
var timeset=null, i = 0, divide = 100;

document.addEventListener('keyup', function(event)
{
   if(event.keyCode == 13)
   {
      if(timeset == null)
	  document.getElementById("start_timer").click();
      else
         document.getElementById("stop_timer").click();
   }
});
function start(){

timeset = self.setInterval('increment()', (1000 / divide));
}

function increment(){

i++;
document.getElementById('time_out').innerHTML = (i / divide);
}

function stop(){
clearInterval(timeset);
timeset = null;
}

function reset(){
stop();
i = 0
document.getElementById('time_out').innerHTML = (i / divide);
}

</script>
</body>
</html>