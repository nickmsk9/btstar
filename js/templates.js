function tpl(id)
{
	jQuery().post('tplget.php',{"id":id},function(response) {
	field.empty(); field.append(response); });
}