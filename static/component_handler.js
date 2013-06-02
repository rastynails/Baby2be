
window.thrown_backend_exceptions = {};

function SK_ComponentHandler(cmp_prototype) {
	var prop;
	for (prop in cmp_prototype) {
		this[prop] = cmp_prototype[prop];
	}

	this.children = [];
	this.forms = [];
}

SK_ComponentHandler.prototype =
{
	DOMConstruct: function(cmp_class, auto_id)
	{
		this.cmp_class = cmp_class;
		this.auto_id = auto_id;

		this.container_node = jQuery('#'+auto_id).get(0);
	},

    /**
     * @return jQuery
     */
	$: function(selector, context) {
		selector = selector.replace('#', '#'+this.auto_id+'-');
		context = context || this.container_node;
		return jQuery(selector, context);
	},

	/**
	 * @return SK_BlockHandler
	 */
	$block: function(selector)
	{
		var $block = this.$(selector);

		if (!$block.length) {
			throw 'block element not found';
		}
		if (!$block.get(0).sk_block_handler) {
			throw 'element has no block handler';
		}

		return $block.get(0).sk_block_handler;
	},

	/**
	 * @return XMLHttpRequest
	 */
	ajax: function(options)
	{
        var handler = this;

            var _opt = {
                url: URL_RESPONDER + "?r=" + Math.random() * Date.parse(new Date()),
                type: 'POST',
                cache: false,
                data: {
                    apply_func: options.apply_func,
                    params: encodeURIComponent( JSON.stringify(options.params) ),
                    COM_node: encodeURIComponent(
                        JSON.stringify( this.getAjaxCOMNode(options.full_COM) )
                    )
                },
                dataType: 'json',
                beforeSend: function(xmlHttp)
                {
                    xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
                },
                success: function(response)
                {
                    if (response.errors) {
                        $(response.errors).each(function() {
                            handler.error(this);
                        });
                    }

                    if (response.exec) {
                        (new Function(response.exec)).call(handler);
                    }

                    if (options.success) {
                        options.success(response.data);
                    }
                },
                error: function(xhr, error) {
                    if (options.error) {
                        options.error(xhr);
                    } else {
                        // handling backend exception
                        var json_e = xhr.getResponseHeader('SK-Exception');
                        if (json_e) {
                            if (window.thrown_backend_exceptions[json_e]) {
                                    return;
                            }

                            eval("var e = "+json_e);

                            e.toString = function() {
                                    return e.message+"\ntrace: "+e.trace_str+"\nfile: "+e.file+" in line: "+e.line;
                            }

                            window.thrown_backend_exceptions[json_e] = e;

                            if (e.class_name == 'SK_HttpRequestException' && e.code == 1) {
                                    // handling "Authentication required" exception
                                    SK_SignIn().bind('close', function() {
                                            window.location.reload();
                                    });
                            }
                            else {
                                    alert('An uncaught exception thrown:\n'+e.toString());
                            }
                        }
                        else if (typeof console != 'undefined' && console !== null ) {
                                console.error('http request error');
                        }
                    }
                }
            };

            if (options.complete) {
                _opt.complete = options.complete;
            }

            return $.ajax(_opt);
	},

	/**
	 * @return XMLHttpRequest
	 */
	ajaxCall: function(apply_func, params, options)
	{
		options = options || {};

		options.apply_func = apply_func;
		options.params = params;

		return this.ajax(options);
	},

	/**
	 * @return XMLHttpRequest
	 */
	reload: function(params, options) {
		params = params || {};
		options = options || {};
		options.full_COM = true;
		return this.ajaxCall('reload', params, options);
	},

	/**
	 * @return object
	 */
	getAjaxCOMNode: function(full_COM)
	{
		var COM_node = {
			cmp_class: this.cmp_class,
			auto_id: this.auto_id,
			forms: []
		};

		if (this.parent) {
			COM_node.parent = {
				cmp_class: this.parent.cmp_class,
				auto_id: this.parent.auto_id
			}
		}

		var i, child, form;

		if (full_COM) {
			COM_node.children = [];
			for (i = 0, child; child = this.children[i]; i++)
			{
				// filtering httpdoc canvas components
				if (this.auto_id === 'httpdoc' &&
					!$('#'+child.auto_id, this.container_node).length) {
					continue;
				}

				COM_node.children.push(child.getAjaxCOMNode(true, false));
			}
		}

		for (i = 0, form; form = this.forms[i]; i++)
		{
			COM_node.forms[i] = {
				form_class: form.form_class,
				auto_id: form.auto_id
			}
		}

		return COM_node;
	},

	error: function(err_msg) {
		SK_drawError(err_msg);
	},


	message: function(msg_text) {
		SK_drawMessage(msg_text);
	},

	debug: function(a) {
		if (console) {
			console.debug(a);
		}
		else {
			alert(JSON.stringify(a));
		}
	}
}
