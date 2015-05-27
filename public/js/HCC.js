
var g_turn = 'black';

function process(data)
{
	var obj = JSON.parse(data);
	if(obj.valid)
	{
		var target = $('#' + obj.step.id);
		target.css({
				'background-color': obj.step.turn,
				'color': (obj.step.turn == 'black' ? 'white' : 'black')
			});
		target.attr('turn', obj.step.turn);

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

			var li = $(document.createElement('li'));
			li.text((obj.step.turn == 'black' ? 'white' : 'black') + ' die at ' + obj.kill[i]);
			li.addClass('killed');
			ul.append(li);
		}

		setDiv.append(ul);
		informBlock.prepend(setDiv);
	}
	else
	{
		var setDiv = $(document.createElement('div'));
		var p = $(document.createElement('p'));
		p.text(obj.step.step + ' : ' + obj.step.turn + ' give up');

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
				data: {
					id: e.target.id,
					turn: g_turn
				},
				success: function(data) {
					
					process(data);

					$.ajax({
						url: '/Go/HCC/HCCRequestNext',
						success: function(data)
						{
							process(data);
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

});