<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sim</title>

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

	.container {
		position: relative;
	}

	.ref {
		text-align: center;
	}
	</style>

</head>
<body>

	<div class=''>

		<table id='t1'>
			@for($i = 0; $i < ($n - 1); $i++)
			<tr>
				@for($j = 0; $j < ($n - 1); $j++)
				{{-- <td class='td1'>{{ $i }}, {{ $j }}</td> --}}
				<td class='td1'></td>
				@endfor
			</tr>
			@endfor
		</table>

		<table id='t2'>
			@for($i = 0; $i < $n; $i++)
			<tr>
				@for($j = 0; $j < $n; $j++)
				{{-- <td class='td2'>{{ $i }}, {{ $j }}</td> --}}
				{{-- <td class='td2' id='t{{ $i }}{{ $j }}'>{{ $i }}, {{ $j }}</td> --}}
				<td class='td2'><div class='cross' id='t{{ $i }}{{ $j }}'></div></td>
				@endfor
			</tr>
			@endfor
		</table>

	</div>

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			var json = '<?php echo $steps ?>';
			//var json = {{ $steps }};
			var obj = JSON.parse(json);
			
			for(var i = 0; i < obj.length; i++)
			{
				//console.log(obj[i].turn + " at " + obj[i].id);
				var target = $('#' + obj[i].id);
				target.css({
						'background-color': obj[i].turn,
						'color': (obj[i].turn == 'black' ? 'white' : 'black')
					});
				target.text(i + 1);
			}

		});
	</script>
</body>
</html>
