<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>History</title>

	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	
	<style type="text/css">

	body {
		background-color: #FFCC99;
	}

	ul {
    	list-style-position: inside;
	}

	#t1 {
		background-color: #CCA37A;

		position: absolute;
		top:200px;
		left: 200px;
	}

	#t2 {
		background-color: rgba(0,0,0, 0.0);

		position: absolute;
		top: 150px;
		left: 160px;
	}

	#playBtn, #stopBtn {
		position: absolute;
		top: 100px;
		left: 100px;
	}

	#information {
		position: relative;
		/*background-color: red;*/
		width: 250px;
		height: 500px;
		top:100px;
		left: 10px;
		text-align: center;
	}

	#allGames {
		position: absolute;
		right:0px;
		top:0px;
		width: 300px;
		/*height: 500px;*/
		/*background-color: red;*/
		font-size: 25px;
	}

	#content {
		position: relative;
		left:300px;
		/*background-color: green;*/
	}

	.td1 {
		width:100px;
		height:100px;
		border-style: solid;
		text-align: center;
	}

	.td2 {
		width:100px;
		height:100px;
		/*border-style: solid;*/
		text-align: center;
		cursor: pointer;
	}

	.cross {
		width:90px;
		height:90px;
		border-radius: 50%;
		line-height: 90px;
	}

	</style>

</head>
<body>

	<div id='content'>
		<a id='playBtn' class="btn btn-default btn-lg">Play</a>
		<a id='stopBtn' class="btn btn-default btn-lg">Stop</a>

		<table id='t1'>
			@for($i = 0; $i < ($n - 1); $i++)
			<tr>
				@for($j = 0; $j < ($n - 1); $j++)
				<td class='td1'></td>
				@endfor
			</tr>
			@endfor
		</table>

		<table id='t2'>
			@for($i = 0; $i < $n; $i++)
			<tr>
				@for($j = 0; $j < $n; $j++)
				<td class='td2'><div class='cross' id='t-{{ $i }}-{{ $j }}'></div></td>
				@endfor
			</tr>
			@endfor
		</table>
	</div>

	<div id='information'>
	</div>

	<div id='allGames'>
		@foreach($allGames as $game)
		<div>
			<a href="/Go/SimAuto/Show/{{ $game->id }}">{{ $game->id }} - {{ $game->created_at }}</a>
		</div>
		@endforeach
	</div>
	

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script>

	var historyIdx = 0;
	var maxLenght= 0;
	var historyInterval;
	var obj = {};

	function playHistory(interval)
	{
		historyInterval = setInterval(function(){

			if(obj[historyIdx].valid)
			{
				var target = $('#' + obj[historyIdx].step.id);
				target.css({
						'background-color': obj[historyIdx].step.turn,
						'color': (obj[historyIdx].step.turn == 'black' ? 'white' : 'black')
				});
				target.text(obj[historyIdx].step.step);

				var informBlock = $('#information');
				//informBlock.append('<p>' + obj.step.step + ' : ' + obj.step.turn + ' at ' + obj.step.id + '</p>');
				var setDiv = $(document.createElement('div'));
				var p = $(document.createElement('p'));
				p.text(obj[historyIdx].step.step + ' : ' + obj[historyIdx].step.turn + ' at ' + obj[historyIdx].step.id);
				
				//informBlock.append(p);
				setDiv.append(p);

				//kill
				var ul = $(document.createElement('ul'));
				for(var j = 0; j < obj[historyIdx].kill.length; j++)
				{
					var victim = $('#' + obj[historyIdx].kill[j]);
					victim.css({
						'background-color': '',
					});
					victim.text('');

					var li = $(document.createElement('li'));
					li.text((obj[historyIdx].step.turn == 'black' ? 'white' : 'black') + ' die at ' + obj[historyIdx].kill[j]);
					ul.append(li);
				}

				setDiv.append(ul);

				informBlock.prepend(setDiv);
			}
			else
			{
				//console.log('invalid move');
				var setDiv = $(document.createElement('div'));
				var p = $(document.createElement('p'));
				p.text(obj[historyIdx].step.step + ' : ' + obj[historyIdx].step.turn + ' give up');

				setDiv.append(p);
				$('#information').prepend(setDiv);
			}

			historyIdx++;
			if(historyIdx >= maxLenght)
			{
				clearInterval(historyInterval);
			}
		}, interval);
	}

	$(document).ready(function() {
		
		var record = '<?php echo $record; ?>';
		obj = JSON.parse(record);
		maxLenght = obj.length;
//		console.log(obj);

		$('#stopBtn').hide();

		$('#playBtn').click(function(){

			if(historyIdx < maxLenght)
			{
				playHistory(1500);

				$('#playBtn').hide();
				$('#stopBtn').show();
			}

		});

		$('#stopBtn').click(function(){

			$('#playBtn').show();
			$('#stopBtn').hide();

			clearInterval(historyInterval);

		})
	});

	</script>


</body>
</html>
