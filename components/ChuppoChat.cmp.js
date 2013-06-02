
function component_ChuppoChat(auto_id)
{
	this.DOMConstruct('ChuppoChat', auto_id);
	
	var handler = this;
	
	this.delegates = {
			
	};
}

component_ChuppoChat.prototype =
	new SK_ComponentHandler({
	
	// Globals
	// Major version of Flash required
	requiredMajorVersion 		: 9,
	// Minor version of Flash required
	requiredMinorVersion 		: 0,
	// Minor version of Flash required
	requiredRevision 			: 0,	
	
	construct: function( actInterval, chuppoChatVars ) {
		var handler = this;		
		loadChat("chuppo_chat_cont", chuppoChatVars);
		window.setInterval( function(){
			handler.updateProfileActivity( handler );
		}, actInterval );
	},

	chatObjectHTML: function() {
		var ret = '';

		var hasProductInstall = DetectFlashVer(6, 0, 65);

		// Version check based upon the values defined in globals
		var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);


		// Check to see if a player with Flash Product Install is available and the version does not meet the requirements for playback
		if ( hasProductInstall && !hasRequestedVersion )
		{
			// MMdoctitle is the stored document.title value used by the installation process to close the window that started the process
			// This is necessary in order to close browser windows that are still utilizing the older version of the player after installation has completed
			// DO NOT MODIFY THE FOLLOWING FOUR LINES
			// Location visited after installation is complete if installation is required
			var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
			var MMredirectURL = window.location;
		    document.title = document.title.slice(0, 47) + " - Flash Player Installation";
		    var MMdoctitle = document.title;
	
			ret += AC_FL_RunContent
			(
				"src", install_host,
				"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"",
				"width", "800",
				"height", "600",
				"align", "middle",
				"id", "chuppochat",
				"quality", "high",
				"bgcolor", "#4d4d4d",
				"name", "chuppochat",
				"allowScriptAccess","sameDomain",
				"type", "application/x-shockwave-flash",
				"pluginspage", "http://www.adobe.com/go/getflashplayer"
			);
		}
		else if (hasRequestedVersion)
		{
			// if we've detected an acceptable version
			// embed the Flash Content SWF when all tests are passed
			ret += AC_FL_RunContent
			(
				"src", swf_host,
				"width", "800",
				"height", "600",
				"align", "middle",
				"id", "chuppochat",
				"quality", "high",
				"bgcolor", "#4d4d4d",
				"name", "chuppochat",
				"flashvars",'historyUrl=history.htm%3F&lconid=' + lc_id + '&userName=' + username + '&gender=' + sex + '&age=' + age + '&' + con_str,
				"allowScriptAccess","sameDomain",
				"type", "application/x-shockwave-flash",
				"pluginspage", "http://www.adobe.com/go/getflashplayer"
			);
	  }
		else
		{  // flash is too old or we can't detect the plugin
			var alternateContent = '<p align="center">You need to have <a href=http://www.adobe.com/go/getflash/>Adobe Flash plugin</a> installed.</p>';
			ret += alternateContent;  // insert non-flash content
		}

		return ret;
	},	
	
	
	loadChatDemo: function() {
		document.getElementById( 'chat_container' ).innerHTML = '<div>' + this.chatObjectHTML() + '</div>';	
	},
		
	
	updateProfileActivity: function( handler ) {
		handler.ajaxCall( 'ajax_updateProfileActivity', {} );
	}	
	
});
