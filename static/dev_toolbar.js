/**
 * Skadate7 development toolbar js-module.
 */

function dev_toolbar_toggle($container) {
	if (dev_toolbar_toggle.hidden) {
		$container.animate({bottom: '0px'}, 'fast');
		dev_toolbar_toggle.hidden = false;
	}
	else {
		$container.animate({bottom: dev_toolbar_toggle.initial_pos}, 'fast');
		dev_toolbar_toggle.hidden = true;
	}
}
dev_toolbar_toggle.hidden = true;

if (jQuery.browser.msie) {
	dev_toolbar_toggle.initial_pos = '-36px';
}
else {
	dev_toolbar_toggle.initial_pos = '-35px';
}


function request_page_recompiling() {
	var search = window.location.search;
	window.location.search = (search ? search+'&' : '?') + 'force_compile=1';
}

$(function() {
	$('body').prepend(
		'<div style="'+
			'position: fixed;'+
			'z-index: 1000;'+
			'right: 0px; bottom: '+dev_toolbar_toggle.initial_pos+';'+
			'background: #A75357;'+
			'border: 1px solid #7F3F42;'+
			'padding: 6px;'+
			'text-align: right;'+
			'">'+
			'<div style="position: relative; z-index: 1">'+
				'<a href="#" title="SK9 Development Toolbar" style="'+
					'position: absolute;'+
					'z-index: -3;'+
					'right: 0px; top: -25px;'+
					'background: #A75357;'+
					'border: 1px solid #7F3F42;'+
					'border-bottom: none;'+
					'color: white;'+
					'font-size: 10px;'+
					'font-weight: bold;'+
					'padding: 2px 4px 1px;'+
					'" onclick="dev_toolbar_toggle($(this).parent().parent()); return false">^</a>'+

				'<input type="button" onclick="request_page_recompiling()" value="Recompile Page" />'+

			'</div>'+
		'</div>');
});
