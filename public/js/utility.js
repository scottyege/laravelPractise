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
