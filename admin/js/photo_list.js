function show_fullsize(src) {
	
	$btn = $jq('<input type="button" value="close" />');
	
	$img = $jq('<img src="' + src + '" />').hide();

	var preparePosition = function(box) {
		
		var position = {
			top:((jQuery(window).height()/2) - (box.$container.height()/2)),
			left:((jQuery(window).width()/2) - (box.$container.width()/2))
		};
		box.$container.css(position);
	}
	
	var box = new SK_FloatBox({
		$contents: $jq('<div class="tmp_placeholder" style="height: 100px; width: 100px; background: url(img/loading.gif) no-repeat center;"></div>'),
		$title: "",
		$controls: $btn
	});
	
	$jq(window).resize(function(){
		preparePosition(box);
	})
	
	box.$body.append($img);
	
	$img.load(function(){
		box.$container.find(".tmp_placeholder").replaceWith($img.show());
		preparePosition(box);
		
		box.bind('show', function(){
			preparePosition(box);
		})
	});
	
	$btn.click(function(){
		box.close();
	});
}

function select_all(input) {
	var $form = $jq(input.form);
	$form.find(".list_item input:checkbox").attr("checked", input.checked);
}