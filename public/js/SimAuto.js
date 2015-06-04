var myInterval;
var isShowColorGroups = false;

function RemoveColorGroups()
{
	$('.cross').removeClass('counting')
				.css({
					'background-color': ''
				});
}

function ColorGroups(emptyGroups)
{
	//console.log('emptygroups', emptyGroups.length);
	for(var i = 0; i < emptyGroups.length; ++i)
	{
		var groupObj = emptyGroups[i];
		var group = groupObj.group;
		var color = (groupObj.color === false ? 'red' : groupObj.color);

		//console.log('color', groupObj.color);
		//console.log('group count', group.length);

		for(var j = 0; j < group.length; ++j)
		{
			var target = $('#' + group[j]);
			//console.log(group[j]);
			target.addClass('counting')
					.css({
						// 'background-color': '',
						'border-color' : color
					});
		}
	}
}

function startAutoRequest(interval)
{
	myInterval = setInterval(function(){
		$.ajax({
				url: '/Go/SimByStep/RequestNext',
				dataType: 'json',
				success: function(obj) {

					console.log(obj);

					if(obj.gameOver !== undefined)
					{
						//console.log('gg');
						//console.log(obj);
						clearInterval(myInterval);
						$('#startBtn').hide();
						$('#saveBtn').show();
						$('#stopBtn').hide();

						if(obj.emptyGroups !== undefined)
						{
							ColorGroups(obj.emptyGroups);
						}
						// if(obj.possibleTerr !== undefined)
						// {
						// 	var emp = obj.possibleTerr;
						// 	for(var i = 0; i < emp.length; i++)
						// 	{
						// 		var target = $('#' + emp[i]);
						// 		target.css({
						// 			'background-color': 'red',
						// 			'border-radius': '50%',
						// 			'width': '40px',
						// 			'height': '40px'
						// 		});
						// 	}
						// }
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
						//console.log(obj);

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

	$('#countBtn').click(function(){

		if(!isShowColorGroups)
		{
			$.ajax({
				url: '/Go/SimAuto/Count',
				dataType: 'json',
				success: function(obj) {
					console.log(obj);
					ColorGroups(obj);
					isShowColorGroups = true;
				}
			});
		}
		else
		{
			RemoveColorGroups();
			isShowColorGroups = false;
		}

	});
});
