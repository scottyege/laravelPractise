@extends('go.Base');

@section('css')
	<style type="text/css">

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
@stop



@section('main')
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

@stop


@section('script')
<script src="{{ asset('/js/SimAuto.js') }}"></script>
@stop

