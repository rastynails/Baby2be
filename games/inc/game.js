document.write('<object id="novelgames_flashGame" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="600" height="400">');
document.write('<param name="movie" value="' + novelgames_gameURL + '" />');
document.write('<param name="allowScriptAccess" value="always" />');
document.write('<param name="allowFullScreen" value="true" />');
document.write('<param name="loop" value="false" />');
document.write('<param name="FlashVars" value="playerID='+ playerID +'&playerName='+ playerName +'" />');
document.write('<param name="menu" value="false" />');
document.write('<param name="quality" value="high" />');
document.write('<param name="wmode" value="' + (window.novelgames_rightClickEnabled ? 'opaque' : 'window') + '" />');
document.write('<embed name="novelgames_flashGame" src="' + novelgames_gameURL + '" ');
document.write('width="600" height="400" ');
document.write('allowScriptAccess="always" ');
document.write('allowFullScreen="true" ');
document.write('loop="false" menu="false" quality="high"  wmode="' + (window.novelgames_rightClickEnabled ? 'opaque' : 'window') + '" ');
document.write('FlashVars="playerID='+playerID+'&playerName='+playerName+'"');
document.write('type="application/x-shockwave-flash" ');
document.write('pluginspage="http://www.macromedia.com/go/getflashplayer">');
document.write('</embed>');
document.write('</object>');
	

if(window.novelgames_rightClickEnabled) {
	novelgames_leftMouseIsDown = false;
	novelgames_rightMouseIsDown = false;
	novelgames_middleMouseIsDown = false;

	function novelgames_mouseDown(e) {
		if (navigator.appName == 'Netscape') {
			//if(e.pageX < 0 || e.pageX >= 800 || e.pageY < 0 || e.pageY >= 600) return;
			if(e.target.name != "novelgames_flashGame") return;
			
			if(e.which == 3) {
				document.novelgames_flashGame.onRightMouseDown();
				e.stopPropagation();
				return false;
			} else if(e.which == 2) {
				document.novelgames_flashGame.onMiddleMouseDown();
				e.stopPropagation();
				return false;
			}
		} else if (navigator.appName == 'Microsoft Internet Explorer') {
			//if(event.clientX < 0 || event.clientX >= 800 || event.clientY < 0 || event.clientY >=600) return;
			if(event.srcElement.id != "novelgames_flashGame") return;
			
			if(event.button & 1 && !novelgames_leftMouseIsDown) {
				novelgames_leftMouseIsDown = true;
			}
			
			if(event.button & 4 && !novelgames_middleMouseIsDown) {
				novelgames_middleMouseIsDown = true;
				window.novelgames_flashGame.onMiddleMouseDown();
			}
			
			if(event.button & 2 && !novelgames_rightMouseIsDown) {
				novelgames_rightMouseIsDown = true;
				window.novelgames_flashGame.onRightMouseDown();
				event.cancelBubble = true;//this actually does nothing to stop the context menu in Flash from appearing
				event.stopPropagation();//this actually generates an error, thus stopping the context menu in Flash from appearing!
				return false;
			}
		}
		return true;
	}

	function novelgames_mouseUp(e) {
		if (navigator.appName == 'Netscape') {
			//if(e.pageX < 0 || e.pageX >= 800 || e.pageY < 0 || e.pageY >= 600) return;
			if(e.target.name != "novelgames_flashGame") return;
			
			if(e.which == 3) {
				document.novelgames_flashGame.onRightMouseUp();
				e.stopPropagation();
				return false;
			} else if(e.which == 2) {
				document.novelgames_flashGame.onMiddleMouseUp();
				e.stopPropagation();
				return false;
			}
		} else if (navigator.appName == 'Microsoft Internet Explorer') {
			//if(event.clientX < 0 || event.clientX >= 800 || event.clientY < 0 || event.clientY >=600) return;
			if(event.srcElement.id != "novelgames_flashGame") return;
			
			if(event.button & 1 && novelgames_leftMouseIsDown) {
				novelgames_leftMouseIsDown = false;
			}
			
			if(event.button & 4 && novelgames_middleMouseIsDown) {
				novelgames_middleMouseIsDown = false;
				window.novelgames_flashGame.onMiddleMouseUp();
			}
			
			if(event.button & 2 && novelgames_rightMouseIsDown) {
				novelgames_rightMouseIsDown = false;
				window.novelgames_flashGame.onRightMouseUp();
				event.cancelBubble = true;//this actually does nothing to stop the context menu in Flash from appearing
				event.stopPropagation();//this actually generates an error, thus stopping the context menu in Flash from appearing!
				return false;
			}
		}
		return true;
	}

	document.onmousedown = novelgames_mouseDown;
	document.onmouseup = novelgames_mouseUp;
	if (document.layers) window.captureEvents(Event.MOUSEDOWN);
	if (document.layers) window.captureEvents(Event.MOUSEUP);
	window.onmousedown = novelgames_mouseDown;
	window.onmouseup = novelgames_mouseUp;
	document.captureEvents(Event.MOUSEDOWN);
	document.getElementById("novelgames_flashGame").addEventListener("mousedown", novelgames_mouseDown, true);
}