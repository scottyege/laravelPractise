<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SimAuto</title>

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

	#startBtn, #stopBtn {
		position: absolute;
		top: 100px;
		left: 100px;
	}

	#saveBtn {
		position: absolute;
		top: 100px;
		left: 200px;
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
	<?php
	    $encrypter = app('Illuminate\Encryption\Encrypter');
	    $encrypted_token = $encrypter->encrypt(csrf_token());
	?>
	<input id="token" type="hidden" value="{{$encrypted_token}}">

	

	<div id='content'>
		<a id='startBtn' class="btn btn-default btn-lg">Start</a>
		<a id='stopBtn' class="btn btn-default btn-lg">Stop</a>

		<a id='saveBtn' class="btn btn-default btn-lg">Save</a>
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

	

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script>

	$(document).ready(function() {
		
		var record = '<?php echo $record; ?>';
		var obj = JSON.parse(record);
		console.log(obj);
		for(var i = 0; i < obj.length; i++)
		{
			if(obj[i].valid)
			{
				var target = $('#' + obj[i].step.id);
				target.css({
						'background-color': obj[i].step.turn,
						'color': (obj[i].step.turn == 'black' ? 'white' : 'black')
				});
				target.text(obj[i].step.step);

				var informBlock = $('#information');
				//informBlock.append('<p>' + obj.step.step + ' : ' + obj.step.turn + ' at ' + obj.step.id + '</p>');
				var setDiv = $(document.createElement('div'));
				var p = $(document.createElement('p'));
				p.text(obj[i].step.step + ' : ' + obj[i].step.turn + ' at ' + obj[i].step.id);
				
				//informBlock.append(p);
				setDiv.append(p);

				//kill
				var ul = $(document.createElement('ul'));
				for(var j = 0; j < obj[i].kill.length; j++)
				{
					var victim = $('#' + obj[i].kill[j]);
					victim.css({
						'background-color': '',
					});
					victim.text('');

					var li = $(document.createElement('li'));
					li.text((obj[i].step.turn == 'black' ? 'white' : 'black') + ' die at ' + obj[i].kill[j]);
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
				p.text(obj[i].step.step + ' : ' + obj[i].step.turn + ' give up');

				setDiv.append(p);
				$('#information').prepend(setDiv);
			}
		}

	});

	</script>


</body>
</html>
