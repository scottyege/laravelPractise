@extends('go.Base');

@section('title')
SimAuto
@stop

@section('css')

	<link rel="stylesheet" type="text/css" href="{{ asset('/css/board.css') }}">
	<style type="text/css">

	#startBtn, #stopBtn {
		position: absolute;
		top: 80px;
		left: 100px;
	}

	#saveBtn {
		position: absolute;
		top: 80px;
		left: 200px;
	}

	</style>
@stop



@section('main')
	<?php
	    $encrypter = app('Illuminate\Encryption\Encrypter');
	    $encrypted_token = $encrypter->encrypt(csrf_token());
	?>
	<input id="token" type="hidden" value="{{$encrypted_token}}">

	<div id='content'>

		@include('go.sub.SubContent')

		<a id='startBtn' class="btn btn-default btn-lg btn-success">Start</a>
		<a id='stopBtn' class="btn btn-default btn-lg btn-danger">Stop</a>
		<a id='countBtn' class="btn btn-default btn-lg btn-danger">Count</a>

		<a id='saveBtn' class="btn btn-default btn-lg btn-warning">Save</a>
		<table id='t1'>
			<?php
				$nminus1 = $n - 1;
			?>
			@for($i = 0; $i < $nminus1; $i++)
			<tr>
				@for($j = 0; $j < $nminus1; $j++)
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

		<div id='horizontalIdx'>
			@for($i = 0; $i < $n; $i++)
			<div class='horizontalIdxCell'>{{ $i }}</div>
			@endfor
		</div>

		<div id='verticalIdx'>
			@for($i = 0; $i < $n; $i++)
			<div class='verticalIdxCell'>{{ $i }}</div>
			@endfor
		</div>
	</div>

	<div id='information'>
	</div>

@stop


@section('script')
<script src="{{ asset('/js/utility.js') }}"></script>
<script src="{{ asset('/js/SimAuto.js') }}"></script>
@stop
