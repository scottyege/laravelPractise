<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title')</title>

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

		#nav {
			position: absolute;
			right: 50px;
			font-size: 25px;
		}
	</style>

	@section('css')
	@show
	
	

</head>
<body>

	<div id='nav'>
		<ul>
			<li><a href="/Go/SimAuto/7/15">產生新棋局</a></li>
		<ul>

		<ol id='allGames'>
			@foreach($allGames as $game)
			<li>
				<a href="/Go/SimAuto/Show/{{ $game->id }}">{{ $game->created_at }}</a>
			</li>
			@endforeach
		</ol>
	</div>
	
	@section('main')
	@show

	<!-- Scripts -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

	@section('script')
	@show

</body>
</html>
