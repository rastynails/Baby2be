
function play_video( object_id, path_to_file, object_height, object_width, media_mode, path_to_swf )
{
	if ( media_mode == 'flash_video' )
	{
		var s1 = new SWFObject( path_to_swf, "single", "300", "300", "7" );
			s1.addParam("allowfullscreen","true");
			s1.addVariable("autostart","false");
			s1.addVariable("showdownload","true");
			s1.addVariable("file", path_to_file );
			s1.addVariable("bufferlength","5");
			s1.write("mediaplayer_container");
	}
	else
	{
		$( object_id ).innerHTML = '';
		$( object_id ).innerHTML = '<object id="mediaobject" name="mediaobject" width="'+object_width+'" height="'+object_height+'"	classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#version=5,1,52,701" standby="loading microsoft windows media player components..." type="application/x-oleobject"> <param name="filename" value="'+path_to_file+'"> <param name="animationatstart" value="false"> <param name="transparentatstart" value="false"> <param name="autostart" value="false"> <param name="showcontrols" value="true"> <param name="showtracker" value="false"> <param name="showaudiocontrols" value="true"> <param name="showstatusbar" value="true"> <embed name="mediaobject" id="mediaobject" type="application/x-mplayer2" width="'+object_width+'" height="'+object_height+'" src="'+path_to_file+'" autostart="0"  showstatusbar="1" showdisplay="0" showcontrols="1" controltype="1" showtracker="0" pluginspage="http://www.microsoft.com/windows/downloads/contents/products/mediaplayer/"></embed></object>'; 	
	}
}


function checkDelete(type) 
{
	if (confirm("Do you really want to delete " + type + "?")) {
		
		$jq('input[name=command]').attr('value','delete');
		$jq("#reports_form_" + type).submit();
		return true;
	}
	return false;
}

function checkSuspend(type) 
{
	if (confirm("Do you really want to suspend " + type + "?")) {
		
		$jq('input[name=command]').attr('value','suspend');
		$jq("#reports_form_" + type).submit();

		return true;
	}
	return false;
}

function checkDismiss(type) 
{
	if (confirm("Do you really want to dismiss report?")) {
		
		$jq('input[name=command]').attr('value','dismiss');
		$jq("#reports_form_" + type).submit();

		return true;
	}
	return false;
}