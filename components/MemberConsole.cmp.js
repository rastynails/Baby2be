function component_MemberConsole(auto_id)
{
	this.DOMConstruct('MemberConsole', auto_id);

	var handler = this;

	this.references = [];

	this.delegates = {

	};
}

component_MemberConsole.prototype =
	new SK_ComponentHandler({

		construct : function() {
			window.sk_component_member_console = this;
			var $menu_cont = this.$(".my_profile_menu_cont");
			var handler = this;

			this.$block("#console_menu").bind("expand", function(){
				handler.ajaxCall("ajax_saveMenuState", {opened:1});
			});

			this.$block("#console_menu").bind("collapse", function(){
				handler.ajaxCall("ajax_saveMenuState", {opened:0});
			});
                        
                        this.$('.delete_thumb').unbind().click(function(){

                        var $delete_msg=$("#delete_t_text").val();

                                SK_confirm($delete_msg, function(){
                                    handler.showPreloader();
                                    handler.ajaxCall('ajax_DeleteThumb');
                                });

			});

		},

		showPreloader: function() {
			var $thumb_cont = this.$(".thumb_container");
			$thumb_cont.addClass("preloader").find("img").hide();
		},

		changeThumb: function(src) {
			var $thumb_cont = this.$(".thumb_container");
			if (src == undefined) {
				$thumb_cont.find("img").show();
				return ;
			}

			var handler = this;
			var $img = $(new Image());

			$img.load(function(){
				$thumb_cont.removeClass("preloader");
				var $prevImage = $thumb_cont.find("img");
				$img.attr("width", $prevImage.width());
				$img.attr("height", $prevImage.height());
				$thumb_cont.find("img").replaceWith($(this));
			});

			$img.attr("src", src);

			this.showThumbDeleteBtn();
		},

		hideThumbDeleteBtn: function()
		{
			this.$('.delete_thumb').hide();
		},

		showThumbDeleteBtn: function()
		{
			this.$('.delete_thumb').show();
		}

});