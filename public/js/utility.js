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

function EndGameWinner(emptyGroups)
{
    var black_score = parseInt($('#black_score').text());
    var white_score = parseInt($('#white_score').text());

    for(var i = 0; i < emptyGroups.length; ++i)
	{
		var groupObj = emptyGroups[i];
		var group = groupObj.group;
        var color = groupObj.color;

        if(color === 'black')
        {
            black_score += group.length;
        }
        else if(color === 'white')
        {
            white_score += group.length;
        }
	}

    console.log('winner: ', (black_score > white_score ? 'black' : 'white')
                , ', black: ', black_score, ', white: ', white_score);
    var winText = (black_score > white_score ? 'black' : 'white')
                + ' wins by '
                + (Math.abs(black_score - white_score))
                + ' points';
    $('#winner').text(winText);
}
