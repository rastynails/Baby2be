function component_ForumTopic(auto_id)
{
	this.DOMConstruct( 'ForumTopic', auto_id );
	
	var handler = this;
	
	this.references = [];
	
	this.delegates = {
		
	};
}

component_ForumTopic.prototype =
	new SK_ComponentHandler({
	
	edit_box : undefined,
	
	move_box : undefined,	
	
	ban_box  : undefined,
	
	mode : "",
	
	construct : function( post_id, forum_id ) {
		var handler = this;	
		
		handler.$("#new_post_btn").bind('click', function(){
			$("textarea[name='post_text']").focus();
		});
		handler.$("#move_topic_btn").bind('click', function(){
			handler.showMoveBox();
		});		
		handler.$( ".reply_post" ).bind( 'click', function(){
			handler.ajaxCall( 'ajax_ReplyPost', {post_id: post_id} );
		} );
		handler.$( ".edit_topic" ).bind( 'click', function(){
			handler.showEditBox();
		} );
		handler.$( ".delete_topic" ).bind( 'click', function(){
			handler.showConfirmBox(forum_id);
		});		
		handler.$( ".lock_topic" ).bind( 'click', function(){
			handler.showConfirmBoxLock();
		});
		handler.$( ".sticky_topic" ).bind( 'click', function(){
			handler.showConfirmBoxSticky();
		});
		handler.$( ".unlock_topic" ).bind( 'click', function(){
			handler.showConfirmBoxUnLock();
		});
		handler.$( ".unsticky_topic" ).bind( 'click', function(){
			handler.showConfirmBoxUnSticky();
		});
		handler.$( ".ban_profile" ).bind( 'click', function(){
			handler.ajaxCall( 'ajax_BanProfile', {post_id: post_id} );
		} );
		
		$(".ctr_btn a", handler.$("#topic_action")).bind('click', function(){
			handler.$block("#topic_action").collapse();
		});
	},

	showEditBox : function(){				
		this.edit_box = new SK_FloatBox({
			$title		: this.$(".edit_topic_title").text(),
			$contents	: this.$(".edit_topic_content"),
			width		: 600,
			position 	: { top : 250, left : 330}
			
		});		
	},
	
	hideEditBox: function() {
		this.edit_box.close();
	},
	
	showMoveBox : function(){				
		this.move_box = new SK_FloatBox({
			$title		: this.$(".move_topic_title").text(),
			$contents	: this.$(".move_topic_content"),
			width		: 300
		});		
	},
	
	hideMoveBox: function() {
		this.move_box.close();
	},	
	
	refreshTopic: function(title, text) {
		var handler = this;		
		
		$(".bread_crumb b").html(title);
		$("#page_content_header").html(title);
		handler.$(".block_cap h3:first").html(title);
		handler.$(".post_text").html(text);		
		handler.hideEditBox();
	},
	
	showConfirmBox: function( forum_id ) {
		var handler = this;
		var topic_id = $("input[name = 'topic_id']").val();	
		
		handler.confirm_box = new SK_confirm( handler.$("#delete_confirm_title").text(), handler.$("#delete_confirm_content").show(), function(){
			handler.ajaxCall( 'ajax_DeleteTopic', {topic_id: topic_id, forum_id: forum_id} );
		});
	},
	
	showConfirmBoxLock: function() {
		var handler = this;
		var topic_id = $("input[name = 'topic_id']").val();	
		
		handler.confirm_box = new SK_confirm( handler.$("#lock_confirm_title").text(), handler.$("#lock_confirm_content").show(), function(){
			handler.ajaxCall( 'ajax_LockTopic', {topic_id: topic_id} );
		});
	},
	
	showConfirmBoxSticky: function() {
		var handler = this;
		var topic_id = $("input[name = 'topic_id']").val();	
		
		handler.confirm_box = new SK_confirm( handler.$("#sticky_confirm_title").text(), handler.$("#sticky_confirm_content").show(), function(){
			handler.ajaxCall( 'ajax_StickyTopic', {topic_id: topic_id} );
			handler.$( ".sticky_topic" ).css( "display", "none" );
			handler.$( ".unsticky_topic" ).css( "display", "inline" );
		});
	},		
	
	showConfirmBoxUnLock: function() {
		var handler = this;
		var topic_id = $("input[name = 'topic_id']").val();	
		
		handler.confirm_box = new SK_confirm( handler.$("#unlock_confirm_title").text(), handler.$("#unlock_confirm_content").show(), function(){
			handler.ajaxCall( 'ajax_UnLockTopic', {topic_id: topic_id} );
		});
	},
	
	showConfirmBoxUnSticky: function() {
		var handler = this;
		var topic_id = $("input[name = 'topic_id']").val();	
		
		handler.confirm_box = new SK_confirm( handler.$("#unsticky_confirm_title").text(), handler.$("#unsticky_confirm_content").show(), function(){
			handler.ajaxCall( 'ajax_unStickyTopic', {topic_id: topic_id} );
			handler.$( ".unsticky_topic" ).css( "display", "none" );
			handler.$( ".sticky_topic" ).css( "display", "inline" );
		});
	},		
	
	replyPost: function( username, text, create_date ) {
		var quote = $("textarea[name=\'post_text\']");
		var quote_text = "[quote name='" + username + "' date='" + create_date + "']\n" + text + "\n[/quote]\n";
		quote.val( quote_text );
		quote.focus();
	},
	
	showBanBox : function( profile_id ){
		$("input[name='profile_id']", ".ban_pr_box_content").val(profile_id); 			
		document.ban_box = new SK_FloatBox({
			$title		: $(".ban_pr_box_title").text(),
			$contents	: $(".ban_pr_box_content"),
			width		: 160			
		});		
	},
		
	hideBanBox: function() {
		document.ban_box.close();
	},	
	
	redirect: function(url) {
		if ( url == undefined ) 
			window.location.reload();
		else 
			window.location.href = url;
	}
	
});

