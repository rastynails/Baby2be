<?php

require_once '../internals/Header.inc.php';

header('Content-Type: text/javascript');


class SkaDate7_DemoTools
{
	/**
	 * Get available themes.
	 *
	 * @return array $name => $label
	 */
	public static function theme_list()
	{
		$result = SK_MySQL::query(
			'SELECT `theme_name`, `title` FROM `'.TBL_THEME.'` ORDER BY `title` ASC'
		);

		$themes = array();
		while ( $row = $result->fetch_object() ) {
			$themes[$row->theme_name] = $row->title;
		}

		return $themes;
	}


	public static function active_theme() {
		return isset($_GET['theme']) ? $_GET['theme'] : SK_Layout::getInstance()->theme();
	}

}

?>

// <script type="text/javascript"> // js highlight

function SkaDateDemoToolbar()
{
	this.theme_list = <?php echo json_encode(SkaDate7_DemoTools::theme_list()) ?>;
	this.active_theme = '<?php echo SkaDate7_DemoTools::active_theme() ?>';

	var options_html;
	for (var key in this.theme_list) {
		options_html +=
			'<option value="'+key+'"'+(
				(key != this.active_theme) ? '' : ' selected="selected"'
			)+'>'+this.theme_list[key]+'</options>';
	}

	jQuery('body').prepend(
		'<div id="demo_toolbar" style="'+
			'position: fixed;'+
			'top: '+SkaDateDemoToolbar.initial_pos+';'+
			'left: 0px;'+
			'z-index: 1000;'+
			'width: 200px;'+
			'border: 1px solid #545454;'+
			'background: url(<?php echo URL_LAYOUT_IMG ?>macFFBgHack.png);'+
			'font-size: 10px;'+
			'padding: 4px;'+
			'">'+

			'<div style="margin-bottom: 10px;">'+

				'<div style="float: right; color: #EFEFEF">'+
					'<a href="http://www.skadate.com/" style="color: #DD7777">Back to SkaDate.com</a> &raquo;<br />'+
				'</div>'+

				'<p style="color: #EFEFEF">'+
					'<a href="http://www.skadate.com/demo/" style="color: #DD7777">User Demo</a><br />'+
					'Username: <b>demo</b><br />'+
					'Password: <b>demo</b>'+
				'</p><hr style="border: 1px inset black" />'+

				'<p style="color: #EFEFEF">'+
					'<a href="http://www.skadate.com/demo/admin/" style="color: #DD7777">Admin Demo</a><br />'+
					'Username: <b>admin</b><br />'+
					'Password: <b>skadate</b>'+
				'</p><hr style="border: 1px inset black" />'+

				'<p style="color: #EFEFEF">Call now: +1 574 203 0674</p>'+

				'<p><a href="#" style="color: #DD7777" onclick="'+
					"window.open('http://messenger.providesupport.com/messenger/skadate_support.html', '_blank', 'menubar=0,location=0,scrollbars=auto,resizable=1,status=0,width=600,height=550')"+
					'">Instant Live Chat Support</a>'+
				'</p><hr style="border: 1px inset black" />'+

				/*'<p>Pay special attention to how different your site may look, not only in colors but also in presense and location of objects such as forms, menu, etc...</p>'+
				'<hr style="border: 1px inset black" />'+*/
			'</div>'+


			'<div style="float: left">'+
				'<b style="color: #EFEFEF">Theme:</b><br />'+
				'<select id="demo_toolbar_theme_switcher">'+options_html+'</select>'+
			'</div>'+

			'<div style="float: right; padding-top: 14px">'+
				'<a id="demo_toolbar_expand" href="#" style="'+
					'line-height: 18px;'+
					'padding: 1px 20px 1px 1px;'+
					'background: url(<?php echo URL_LAYOUT_IMG ?>block_expand.png) no-repeat right top;'+
					'font-size: 11px;'+
					'color: #DD7777;'+
					'">Demo Info</a>'+
			'</div>'+

			'<br clear="all" />'+

		'</div>'
	);


	var handler = this;

	this.expanded = false;

    jQuery('#demo_toolbar_expand')
		.click(function() {
			if (handler.expanded) {
				handler.collapse();
			}
			else {
				handler.expand();
			}
			return false;
		}
	);

	jQuery('#demo_toolbar_theme_switcher')
		.change(function() {
			var search = window.location.search;

			if (search) {
				var searchArr = [];
				jQuery.each(search.substr(1).split('&'), function(i, item){
					var foo = item.split('=');
					if (foo[0] != 'layout') {
						searchArr.push(item);
					}
				});

				search = '?' + searchArr.join('&');
			}

			window.location.search = (search ? search+'&' : '?')+'layout='+this.value;
		});
}

if (jQuery.browser.msie) {
	SkaDateDemoToolbar.initial_pos = '-182px';
}
else { // other
	SkaDateDemoToolbar.initial_pos = '-164px';
}

SkaDateDemoToolbar.prototype =
{
	expand: function() {
		jQuery('#demo_toolbar').animate({top: '0px'}, 'fast', function() {
			jQuery('#demo_toolbar_expand').css('background-image', 'url(<?php echo URL_LAYOUT_IMG ?>block_collapse.png)');
		});
		this.expanded = true;
	},

	collapse: function() {
		jQuery('#demo_toolbar').animate({top: SkaDateDemoToolbar.initial_pos}, 'fast', function() {
			jQuery('#demo_toolbar_expand').css('background-image', 'url(<?php echo URL_LAYOUT_IMG ?>block_expand.png)');
		});
		this.expanded = false;
	}
}

jQuery(function() {
	window.sk_demo_toolbar = new SkaDateDemoToolbar();
});
