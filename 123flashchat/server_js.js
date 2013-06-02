load_config();

jQuery("#submit").click(function(){

var post = new Object(); 
post.fc_running_mode = jQuery("#fc_running_mode").val();
post.fc_client_location =  jQuery("#fc_client_location").val();
post.fc_room_name =  jQuery("#fc_room_name").val();



var jsontext = JSON.stringify(post);

	jQuery.post('../123flashchat/123flashchat_ajax.php',{
			fc_json: jsontext,
			key: 'dzhasdiweasdlkas121312qdhiu2daczxc_save_config'
			},function(data){
				if(data == 'saved')
				{
				 jQuery("#setting_saved").replaceWith('<font id="setting_saved" color="#FF0000">Settings saved.</font>');
				 load_config();
				}
				else if(data == 'error')
				{
					jQuery("#setting_saved").replaceWith('<font id="setting_saved" color="#FF0000">Please adapt the file (123flashchat/123flashchat_config.php) authorization into writable one</font>');
				}
		});
	
});

function load_config()
{
	jQuery.post('../123flashchat/123flashchat_ajax.php',{
			key: 'WEDFVasdzz23s121312qdhiu2daczxc_load_config'
			},function(data){
				var json = JSON.parse(data);
				if(json.fc_client_location != '')
				{
					jQuery("#fc_client_location").replaceWith('<input name="fc_client_location" type="text" class="input_text" id="fc_client_location" value="'+json.fc_client_location+'" size="60">');
				}
				if(json.fc_room_name != '')
				{
					jQuery("#fc_room_name").replaceWith('<input name="fc_room_name" type="text" class="input_text" id="fc_room_name" value="'+json.fc_room_name+'" size="60">');
				}
				if(json.fc_running_mode != '')
				{
					var select_content = '<select name="fc_running_mode" id="fc_running_mode"><option value="1" ';
					if(json.fc_running_mode == '1')
					{
						select_content += 'selected="selected" ';
					}
					select_content += ' >Chat server is hosted by your own</option><option value="2" ';
					if(json.fc_running_mode == '2')
					{
						select_content += 'selected="selected" ';
					}
					select_content += ' >Chat server is hosted by 123FlashChat.com</option><option value="0" ';
					if(json.fc_running_mode == '0')
					{
						select_content += 'selected="selected" ';
					}
					select_content += ' >Chat server is hosted by 123FlashChat.com free of charge</option></select>';
					
				 	jQuery("#fc_running_mode").replaceWith(select_content);
				}
		});
}

///////json code /////////////////////////////

var JSON = function () {
    var m = {
		'\b': '\\b',
		'\t': '\\t',
		'\n': '\\n',
		'\f': '\\f',
		'\r': '\\r',
		'"' : '\\"',
		'\\': '\\\\'
	},
	s = {
		'boolean': function (x) {
			return String(x);
		},
		number: function (x) {
			return isFinite(x) ? String(x) : 'null';
		},
		string: function (x) {
			if (/["\\\x00-\x1f]/.test(x)) {
				x = x.replace(/([\x00-\x1f\\"])/g, function(a, b) {
					var c = m[b];
					if (c) {
						return c;
					}
					c = b.charCodeAt();
					return '\\u00' +
						Math.floor(c / 16).toString(16) +
						(c % 16).toString(16);
				});
			}
			return '"' + x + '"';
		},
		object: function (x) {
			if (x) {
				var a = [], b, f, i, l, v;
				if (x instanceof Array) {
					a[0] = '[';
					l = x.length;
					for (i = 0; i < l; i += 1) {
						v = x[i];
						f = s[typeof v];
						if (f) {
							v = f(v);
							if (typeof v == 'string') {
								if (b) {
									a[a.length] = ',';
								}
								a[a.length] = v;
								b = true;
							}
						}
					}
					a[a.length] = ']';
				} else if (x instanceof Object) {
					a[0] = '{';
					for (i in x) {
						v = x[i];
						f = s[typeof v];
						if (f) {
							v = f(v);
							if (typeof v == 'string') {
								if (b) {
									a[a.length] = ',';
								}
								a.push(s.string(i), ':', v);
								b = true;
							}
						}
					}
					a[a.length] = '}';
				} else {
					return;
				}
				return a.join('');
			}
			return 'null';
		}
	};
    return {
        copyright: '(c)2005 JSON.org',
        license: 'http://www.JSON.org/license.html',
/*
    Stringify a JavaScript value, producing a JSON text.
*/
        stringify: function (v) {
            var f = s[typeof v];
            if (f) {
                v = f(v);
                if (typeof v == 'string') {
                    return v;
                }
            }
            return null;
        },
/*
    Parse a JSON text, producing a JavaScript value.
    It returns false if there is a syntax error.
*/
        parse: function (text) {
            try {
                return !(/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/.test(
                        text.replace(/"(\\.|[^"\\])*"/g, ''))) &&
                    eval('(' + text + ')');
            } catch (e) {
                return false;
            }
        }
    };
}();