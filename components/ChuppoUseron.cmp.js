
function component_ChuppoUseron(auto_id)
{
	this.DOMConstruct('ChuppoUseron', auto_id);
	
	var handler = this;
	
	this.delegates = {
			
	};
}

component_ChuppoUseron.prototype =
	new SK_ComponentHandler({
		
	construct: function( url_home, chuppoUseronVars) {
		window.SITE_URL = url_home;	
		loadUseronim( 'chuppo_useronim_cont', chuppoUseronVars );

	}
});

function OpenIm( userKey, oppUserKey ) {
	window.ESD_currentIM = window.open( window.SITE_URL+'member/im.php?userKey=' + userKey + '&oppUserKey=' + oppUserKey + '&userName=' + 'qwe', '', 'height=570,width=470,left=100,top=100,scrollbars=no,resizable=no' );
//		window.open( window.SITE_URL+'member/im.php?userKey=' + userKey + '&oppUserKey=' + oppUserKey + '&userName=' + 'qwe', '', 'height=570,width=470,left=100,top=100,scrollbars=no,resizable=no' );	
	return false;
}
