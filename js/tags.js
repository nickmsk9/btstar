function tag_switch()
{
	switch(tagNow)
	{
		case 'cloud':
			tagNow = 'tags';
		break;
		case 'tags':
			tagNow = 'cloud';
		break;
	}
	tagRotate();
	tag_puts();
}

function tag_puts()
{
	switch(tagNow)
	{
		case 'tags':
			jQuery.post("tagListj.php",{},function (response) {
				jQuery('#tagscc').empty().append(response); });
		break;
		case 'cloud':
			jQuery.post("tagCloudj.php",{},function (response) {
				jQuery('#tagscc').empty().append(response); });
		break;
	}
}

function tagRotate()
{
	switch(tagNow)
	{
		case 'tags':
			jQuery('#tagcchead').empty().append('<a href="javascript:tag_switch();" title="Облако"><img src="pic/l-arrow.gif" alt="Облако" title="Облако" border=\"0\"></a> Теги');
		break;
		case 'cloud':
			jQuery('#tagcchead').empty().append('Облако <a href="javascript:tag_switch();" title="Теги"><img src="pic/r-arrow.gif" alt="Теги" title="Теги" border=\"0\"></a>');
		break;
	}
}