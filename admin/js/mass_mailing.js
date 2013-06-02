function checkComposeMsgForm()
{
	if ( !$( 'msg_subject' ).value.trim() )
		return fail_alert( $( 'msg_subject' ), 'Missing message subject' );
		
	if ( !$( 'msg_text' ).value.trim() )
		return fail_alert( $( 'msg_text' ), 'Missing message text' );
		
	return true;
}

function toggleMsgTypeContainers( node )
{
	var node_id = node.id;
	
	if ( node_id == 'msg-external' && node.checked )
	{
		$jq("#email-label").show();
		$jq("#email-value").show();
		$jq("#as-html").attr("disabled", false);
		$jq("#as-html").attr("checked", false);
	}
	else if ( node_id == 'msg-internal' && node.checked )
	{
		//$jq("#cont-msg-external").hide();
		$jq("#email-label").hide();
		$jq("#email-value").hide();		
		$jq("#as-html").attr("disabled", true);
		$jq("#as-html").attr("checked", true);
	}
}