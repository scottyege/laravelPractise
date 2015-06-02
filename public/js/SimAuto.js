var myInterval;

function startAutoRequest(interval)
{
	myInterval = setInterval(function(){
		$.ajax({
				url: '/Go/SimByStep/RequestNext',
				dataType: 'json',
				success: function(obj) {

					if(obj.gameOver !== undefined)
					{
						console.log('gg');

						clearInterval(myInterval);
						$('#startBtn').hide();
						$('#saveBtn').show();
						$('#stopBtn').hide();

						return;
					}

					if(obj.valid)
					{
						var target = $('#' + obj.step.id);
						target.css({
								'background-color': obj.step.turn,
								'color': (obj.step.turn == 'black' ? 'white' : 'black')
							});
						target.text(obj.step.step);
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
							victim.text('');
							victim.removeClass('occupied');

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
			});	
		}, interval);
}

$(document).ready(function() {
	
	$('#stopBtn').hide();
	$('#saveBtn').hide();

	$('#startBtn').click(function(){
		startAutoRequest(1000);

		$('#startBtn').hide();
		$('#saveBtn').hide();
		$('#stopBtn').show();
	});

	$('#stopBtn').click(function(){
		clearInterval(myInterval);

		$('#startBtn').show();
		$('#saveBtn').show();
		$('#stopBtn').hide();
	})

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
});