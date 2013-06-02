
function component_ProfileVideoAlbum(auto_id)
{
	this.DOMConstruct('ProfileVideoAlbum', auto_id);
	
	var handler = this;
	
}

component_ProfileVideoAlbum.prototype =
	new SK_ComponentHandler({
	
	construct: function( params )
	{
		var handler = this;
		
		var $btn1 = this.$('ul.menu-ajax-block li a:eq(0)');
		var $btn2 = this.$('ul.menu-ajax-block li a:eq(1)');
		
		var $menu_item1 = this.$('ul.menu-ajax-block li:eq(0)');
		var $menu_item2 = this.$('ul.menu-ajax-block li:eq(1)');
		
		var $form_2 = this.$("#video_latest");
		var $form_1 = this.$("#video_toprated");

		$btn1.click(function(){
			$btn1.addClass('active');
			$menu_item1.addClass('active');
			$btn2.removeClass('active');
			$menu_item2.removeClass('active');
			
			$form_1.fadeOut('fast', function(){
				$form_2.fadeIn('fast');
			});
		});
		
		$btn2.click(function(){
			$btn2.addClass('active');
			$menu_item2.addClass('active');
			$btn1.removeClass('active');
			$menu_item1.removeClass('active');
		
			$form_2.fadeOut('fast', function(){
				$btn2.addClass('active');
				$form_1.fadeIn('fast');
			});
		});	
		
		var $player = $("iframe", ".profile_video_album");
                var url = $player.attr("src");
                if ( url != undefined )
                {
                        var wmode = "wmode=transparent";
                        if ( url.indexOf("?") != -1)
                            $player.attr("src", url + "&" + wmode);
                        else
                            $player.attr("src", url + "?" + wmode);
                }

                this.$("#latest_unlock form").submit(function(){
			handler.ajaxCall("ajaxUnlock", {hash: params.latestHash, profile_id: params.profile_id, password: $(this.password).val()});
			return false;
		});

                this.$("#top_unlock form").submit(function(){
			handler.ajaxCall("ajaxUnlock", {hash: params.topHash, profile_id: params.profile_id, password: $(this.password).val()});
			return false;
		});

	},

        refresh: function()
        {
            window.location.reload();
	}
	
});
