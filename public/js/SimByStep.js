$(document).ready(function() {
	
	$('#nextBtn').click(function(e){
		$.ajax({
			url: '/Go/SimByStep/RequestNext',
			success: function(data) {
				//alert(data);
				console.log(data);

				var obj = JSON.parse(data);
		

				if(obj.valid)
				{
					var target = $('#' + obj.step.id);
					target.css({
							'background-color': obj.step.turn,
							'color': (obj.step.turn == 'black' ? 'white' : 'black')
						});
					target.text(obj.step.step);

					var informBlock = $('#information');
					//informBlock.append('<p>' + obj.step.step + ' : ' + obj.step.turn + ' at ' + obj.step.id + '</p>');
					var p = $(document.createElement('p'));
					p.text(obj.step.step + ' : ' + obj.step.turn + ' at ' + obj.step.id);
					
					informBlock.append(p);

					//kill
					var ul = $(document.createElement('ul'));
					for(var i = 0; i < obj.kill.length; i++)
					{
						var victim = $('#' + obj.kill[i]);
						victim.css({
							'background-color': '',
						});
						victim.text('');

						var li = $(document.createElement('li'));
						li.text((obj.step.turn == 'black' ? 'white' : 'black') + ' die at ' + obj.kill[i]);
						ul.append(li);
					}

					informBlock.append(ul);
				}
				else
				{
					//console.log('invalid move');
				}
			}
		});	
	});

	

});