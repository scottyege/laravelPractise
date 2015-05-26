@extends('go.Base');

@section('title')
Human Computer Chess
@stop

@section('css')
	<style type="text/css">

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

	#horizontalIdx {
		/*background-color: red;*/
		font-size: 20px;
		z-index: -100;

		position: absolute;
		top:95px;
		left:130px;
	}

	#verticalIdx {
		/*background-color: red;*/
		font-size: 20px;

		position: absolute;
		top:150px;
		left:95px;
	}

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

	#information {
		position: absolute;
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

	.horizontalIdxCell {
		width:100px;
		height:100px;

		display: inline-block;
		text-align: center;
		line-height: 100px;
	}

	.verticalIdxCell {
		width:100px;
		height:100px;

		text-align: center;
		line-height: 100px;
	}

	.cross {
		width:90px;
		height:90px;
		border-radius: 50%;
		line-height: 90px;
	}

	.killed {
		color: red;
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
<script src="{{ asset('/js/HCC.js') }}"></script>
@stop

