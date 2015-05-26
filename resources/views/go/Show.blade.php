@extends('go.Base');

@section('title')
{{ $created_at }}
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

	#playBtn, #stopBtn {
		position: absolute;
		top: 80px;
		left: 100px;
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
		background-color: green;
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
	<div id='content'>
		<a id='playBtn' class="btn btn-default btn-lg">Play</a>
		<a id='stopBtn' class="btn btn-default btn-lg">Stop</a>

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
					li.addClass('killed');
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
				$('#playBtn').show();
				$('#stopBtn').hide();
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
@stop

