function component_ForumPostList(auto_id)
{
	this.DOMConstruct('ForumPostList',auto_id);
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ForumPostList.prototype =
	new SK_ComponentHandler({
	
	edit_box : [],
	
	ban_box : undefined,
	
	confirm_box: undefined,
	
	items: [],
	
	mode : "",
	
	construct : function( posts, cur_page ){
		var handler = this;		

		$.each( posts, function(index, post_id){
			var $item_node = handler.$("#post_" + post_id);
			handler.items[post_id] = $item_node;
			
			handler.$( ".edit_post", $item_node ).bind( 'click', function(){
				handler.ajaxCall( 'ajax_EditPost', {post_id: post_id} );			
			} );
			handler.$( ".delete_post", $item_node ).bind( 'click', function(){
				handler.showConfirmBox( post_id, cur_page );
			} );
			handler.$( ".reply_post", $item_node ).bind( 'click', function(){
				handler.ajaxCall( 'ajax_ReplyPost', {post_id: post_id} );
			} );
			handler.$( ".ban_profile", $item_node ).bind( 'click', function(){
				handler.ajaxCall( 'ajax_BanProfile', {post_id: post_id} );
			} );
		} );
	},
	
	profileNotify : function() {
		var handler = this;
		handler.$( ".topic_sub" ).bind( 'click', function(){
			handler.showSubscribeConfirm();
		} );
		
		handler.$( ".topic_unsub" ).bind( 'click', function(){
			handler.showUnSubscribeConfirm();
		} );		
	},
		
	showEditBox : function( post_id, post_text ){
		 
		this.$("input[name = 'post_id']").val(post_id);
		this.$("textarea[name = 'edit_post_text']").val(post_text);
				
		this.edit_box[post_id] = new SK_FloatBox({
			$title		: this.$(".editbox_title").text(),
			$contents	: this.$(".editbox_content"),
			width		: 600,
			position 	: { top : 250, left : 330}
			
		});		
	},
	
	
	hideEditBox: function(post_id) {
		this.edit_box[post_id].close();
	},
	
	showBanBox : function( profile_id ){
		this.$("input[name='profile_id']", ".ban_pr_box_content").val(profile_id); 			
		document.ban_box = new SK_FloatBox({
			$title		: this.$(".ban_pr_box_title").text(),
			$contents	: this.$(".ban_pr_box_content"),
			width		: 160			
		});		
	},
		
	hideBanBox: function() {
		document.ban_box.close();
	},	
	
	refreshPostText: function(id, text) {
		var handler = this;		
		var $node = this.items[id];
		
		$(".post_text", $node).html(text);		
		handler.hideEditBox(id);
	},
	
	showConfirmBox: function( post_id, cur_page ) {
		var handler = this;
		var topic_id = this.$("input[name = 'topic_id']").val();	
		
		handler.confirm_box = new SK_confirm( handler.$("#confirm_title").text(), handler.$("#confirm_content").show(), function(){
			handler.ajaxCall( 'ajax_DeletePost', {post_id: post_id, topic_id: topic_id, cur_page: cur_page} );
		});
	},
	
	showSubscribeConfirm: function() {
		var handler = this;
		var topic_id = this.$("input[name = 'topic_id']").val();	
		
		handler.confirm_box = new SK_confirm( handler.$("#track_replies_title").text(), handler.$("#track_replies_content").show(), function(){
			handler.ajaxCall( 'ajax_UpdateSubscribe', { topic_id: topic_id } );
			handler.$( ".topic_sub" ).css( "display", "none" );
			handler.$( ".topic_unsub" ).css( "display", "inline" );
		});
	},	
		
	showUnSubscribeConfirm: function() {
		var handler = this;
		var topic_id = this.$("input[name = 'topic_id']").val();	
		
		handler.confirm_box = new SK_confirm( handler.$("#unsub_title").text(), handler.$("#unsub_content").show(), function(){
			handler.ajaxCall( 'ajax_UpdateSubscribe', { topic_id: topic_id } );
			handler.$( ".topic_unsub" ).css( "display", "none" );
			handler.$( ".topic_sub" ).css( "display", "inline" );
		});
	},
	
	replyPost: function( username, text, create_date ) {
		var handler = this;	
		var quote = this.$("textarea[name=\'post_text\']");
		var quote_text = "[quote name='" + username + "' date='" + create_date + "']\n" + text + "\n[/quote]\n";
		quote.val( quote_text );
		quote.focus();
	},
	
	redirect: function(url) {
		if ( url == undefined ) 
			window.location.reload();
		else 
			window.location.href = url;
	}
	
});

