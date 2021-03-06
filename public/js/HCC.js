
var g_turn = 'black';

function process(obj)
{

	if(obj.gameOver !== undefined)
	{

		if(obj.emptyGroups !== undefined)
		{
			ColorGroups(obj.emptyGroups);
			EndGameWinner(obj.emptyGroups);
		}

		return;
	}

	if(obj.valid)
	{
		var target = $('#' + obj.step.id);
		target.css({
				'background-color': obj.step.turn,
				'color': (obj.step.turn == 'black' ? 'white' : 'black')
			});
		target.attr('turn', obj.step.turn);
		target.addClass('occupied');

		var informBlock = $('#information');
		var setDiv = $(document.createElement('div'));
		var p = $(document.createElement('p'));
		p.text(obj.step.step + ' : ' + obj.step.turn + ' at ' + obj.step.id);

		setDiv.append(p);

		//kill
		var ul = $(document.createElement('ul'));
		for(var i = 0; i < obj.kill.length; i++)
		{
			var victim = $('#' + obj.kill[i]);
			victim.css({
				'background-color': '',
			});
			victim.attr('turn', '');
			victim.removeClass('occupied');


			var li = $(document.createElement('li'));
			li.text((obj.step.turn == 'black' ? 'white' : 'black') + ' die at ' + obj.kill[i]);
			li.addClass('killed');
			ul.append(li);
		}

		//update kill count
		var scoreDiv = $('#' + obj.step.turn + '_score');
		var newScore = parseInt(scoreDiv.text()) + obj.kill.length;
		scoreDiv.text(newScore);

		setDiv.append(ul);
		informBlock.prepend(setDiv);
	}
	else
	{

		//console.log(obj.msg);

		var setDiv = $(document.createElement('div'));
		var p = $(document.createElement('p'));
		p.text(obj.step.step + ' : ' + obj.step.turn + ' passes');

		setDiv.append(p);
		$('#information').prepend(setDiv);
	}
}

$(document).ready(function() {

	$('.cross').attr('turn', '');

	$('.td2').click(function(e){

		var target = $('#' + e.target.id);

		if(target.hasClass('cross') && target.attr('turn') === '')
		{
			$.ajax({
				url: '/Go/HCC/CheckValidState',
				dataType: 'json',
				data: {
					id: e.target.id,
					turn: g_turn
				},
				success: function(obj) {

					process(obj);

					$.ajax({
						url: '/Go/SimByStep/RequestNext',
						dataType: 'json',
						success: function(obj)
						{
							process(obj);
						}
					});
				}
			});
		}
		else
		{
			console.log('invalid move');
		}

	});

	$('#saveBtn').click(function(){
		$.ajax({
			url: '/Go/SimAuto/Store',
			dataType: 'json',
			//headers: { 'X-XSRF-TOKEN' : $('#token').val() },
			success: function(obj){
				var ol = $('#allGames');
				ol.prepend("<li><a href='/Go/SimAuto/Show/" + obj.id + "'>" + obj.created_at + "</a></li>");
			}
		});
	})

	$('#passBtn').click(function(){
		$.ajax({
			url: '/Go/HCC/CheckValidState',
			dataType: 'json',
			data: {
				pass: 'pass'
			},
			success: function(obj) {

				process(obj);

				if(obj.gameOver !== undefined)
				{
					return;
				}

				$.ajax({
					url: '/Go/SimByStep/RequestNext',
					dataType: 'json',
					success: function(obj)
					{
						//console.log(obj);
						process(obj);
					}
				});
			}
		});
	});
});
