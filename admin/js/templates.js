function AdminTemplates( img_dir, current )
{
	var handler = this;
	
	this.img_dir = img_dir;
	this.current = current;
	
	$jq("#thumb_list a")
		.click(function() {
			handler.selectThumb(
				$jq(this).attr("rel"), 
				$jq(this).find('img').attr('src'),
				$jq(this).find('span').html(),
				$jq(this).find('div').html(),
				handler.current
			);
		});

}

AdminTemplates.prototype = 
{
	selectThumb: function( theme_name, img, details, title, current )
	{
		$jq('#tpl_details input[type=hidden]').val(theme_name);
		$jq('#tpl_details').find('img').attr('src', img);
		$jq('#tpl_details #info').html('<span>'+details+'</span>');
		$jq('#tpl_details #tpl_name').html('<b>'+title+'</b>');
		
		if (current == theme_name)
			$jq('#submit_theme').css('display','none');
		else
			$jq('#submit_theme').css('display','');
	}
}