@extends('go.Base');

@section('title')
{{ $created_at }}
@stop

@section('css')

	<link rel="stylesheet" type="text/css" href="{{ asset('/css/board.css') }}">

	<style type="text/css">

	#playBtn, #stopBtn {
		position: absolute;
		top: 80px;
		left: 100px;
	}

	</style>
@stop



@section('main')

	<div id='content'>

		@include('go.sub.SubContent')

		<a id='playBtn' class="btn btn-success btn-lg">Play</a>
		<a id='stopBtn' class="btn btn-danger btn-lg">Stop</a>

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
<script>

	var historyIdx = 0;
	var maxLenght= 0;
	var historyInterval;
	var obj = {};

	function playHistory(interval)
	{
		historyInterval = setInterval(function(){

			if(obj[historyIdx].gameOver !== undefined)
			{
				if(obj[historyIdx].emptyGroups !== undefined)
				{
					ColorGroups(obj[historyIdx].emptyGroups);
					EndGameWinner(obj[historyIdx].emptyGroups);
				}

				clearInterval(historyInterval);
				$('#playBtn').show();
				$('#stopBtn').hide();

				return;
			}

			if(obj[historyIdx].valid)
			{
				var target = $('#' + obj[historyIdx].step.id);
				target.css({
						'background-color': obj[historyIdx].step.turn,
						'color': (obj[historyIdx].step.turn == 'black' ? 'white' : 'black')
				});
				target.text(obj[historyIdx].step.step);

				var informBlock = $('#information');

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

				//update kill count
				var scoreDiv = $('#' + obj[historyIdx].step.turn + '_score');
				var newScore = parseInt(scoreDiv.text()) + obj[historyIdx].kill.length;
				scoreDiv.text(newScore);

				setDiv.append(ul);

				informBlock.prepend(setDiv);
			}
			else
			{
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
				playHistory(500);

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
