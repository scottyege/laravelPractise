<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel</title>

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
		background-color: red;

		position: absolute;
		top:100px;
		left: 100px;
	}

	#t2 {
		background-color: rgba(0,0,0, 0.0);

		position: absolute;
		top: 50px;
		left: 50px;
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

	.container {
		position: relative;
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
				<td class='td2' id='t{{ $i }}{{ $j }}'>{{ $i }}, {{ $j }}</td>
				@endfor
			</tr>
			@endfor
		</table>

	</div>

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script type="text/javascript">

	var turn = 0;

	$(document).ready(function() {
		$('.td2').click(function(e){
			//alert('td2');

			var target = $(e.target);

			if(target.attr('claimed') != undefined)
			{
				return;
			}

			target.attr('claimed', true);

			target.css('border-radius', '50%');

			if(turn == 0)
			{
				target.css('background-color', 'black');				
			}
			else
			{
				target.css('background-color', 'white');
			}
			turn = !turn;	
		});

		$('.td1').click(function(e){
			alert('td1');
		});
	});

	</script>
</body>
</html>
