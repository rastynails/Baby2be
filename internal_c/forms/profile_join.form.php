<?php
$form = form_Join::__set_state(array(
   'step' => NULL,
   'session' => NULL,
   'avalible_actions' => 
  array (
  ),
   'active_action' => 'action_1___',
   'class' => 'form_Join',
   'name' => 'profile_join',
   'fields' => 
  array (
    'captcha' => 
    field_captcha::__set_state(array(
       'name' => 'captcha',
       'class' => 'field_captcha',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {
			var handler = this;
			
			
			$input.parents(".captcha_container:eq(0)").find("a").click(function(){
				handler.refresh_image($input); 
			});
			
			form_handler.bind("error", function(){
				handler.refresh_image($input); 
			});
		}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
        '$input' => '{}',
        'request_result' => '""',
        'refresh_image' => 'function($input){
				$.ajax({
							url: "http://www.baby2be.dk/field_responder.php",
							method: "post",
							dataType: "json",
							data: {action: "change_captcha_image"},
							success: function(result){
							 	if (result){
									$input.parents(".captcha_container:eq(0)").find("img").attr("src","http://www.baby2be.dk/captcha/image.php?img_id="+result);
									$input.val("");								
								}
							 }
						});
		}',
      ),
    )),
    'occupation' => 
    fieldType_text::__set_state(array(
       'maxlength' => 128,
       'minlength' => 0,
       'regex' => 
      array (
        0 => '/.{2,}/',
        1 => NULL,
      ),
       'size' => NULL,
       'name' => 'occupation',
       'class' => 'fieldType_text',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '134',
    )),
    'username' => 
    field_join_username::__set_state(array(
       'maxlength' => 32,
       'minlength' => 0,
       'regex' => 
      array (
        0 => '/^\\w+$/',
        1 => NULL,
      ),
       'size' => NULL,
       'name' => 'username',
       'class' => 'field_join_username',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => '
					function($input) {
						var handler = this;

						if($input.val()!="" && $input.val()!=handler.lastValue){
							handler.check_username($input);
						}

						$input.change(function(){
							handler.changed = true;
						});

						$input.blur(function() {
							if($input.val()!="" && $input.val()!=handler.lastValue && handler.changed ){
								handler.check_username($input);
							}
							else{
								$input.parent().find(".success").remove();
							}
						});
					}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
        'changed' => 'true',
        'lastValue' => '""',
        'check_username' => '
					function($input){

					var handler = this;
						$.ajax({
								url: "http://www.baby2be.dk/field_responder.php",
								method: "post",
								dataType: "json",
								data: {action: "check_username_exists", username: $input.val()},
								success: function(result){
										$input.parent().find(".success").remove();
										handler.changed = false;
                                        if(result==1){
   											$input.after(\'<span class="success">&nbsp;</span>\');
   										}
   										else{
   											SK_drawError(result);
   										}   										
  								}
							});
					}
					',
      ),
       'profile_field_id' => '2',
    )),
    'location' => 
    field_location::__set_state(array(
       'items' => 
      array (
      ),
       'labelsection' => NULL,
       'countries' => NULL,
       'name' => 'location',
       'class' => 'field_location',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => '
			function($input, formHandler, auto_id){
				var handler = this;
				this.$errorContainer = $("#"+auto_id+"-container");
				this.$parent_node = $input.parents("tbody:eq(0)");
				this.$container_node = $input.parents(".location_container:eq(0)");
				if ($input.val()!=""){

					this.$parent_node.find("tr.state_id select").change(function(){
					    handler.$errorContainer.empty();
						handler.change_event($(this),$input.val());
					});

					this.$parent_node.find("input[type=button]").click(function(){
					    handler.$errorContainer.empty();
						handler.change_event(handler.$parent_node.find("tr.zip input[type=text]"),$input.val());
					});
					this.suggest(this.$parent_node.find("input[name*=city_name]"),this.$parent_node.find("tr.state_id select").val());
				}

				$input.change(function(){
				    handler.$errorContainer.empty();
					handler.change_event($input);
				});
			}
		',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
        '$parent_node' => '{}',
        '$container_node' => '{}',
        'fields' => '[]',
        '$prototype_node' => '{}',
        'change_event' => '
			function($input, param){
				var handler = this;

				handler.fields[$input.parents("tr:eq(0)").attr("class")] = $input.val();
				$.ajax({
							url: "http://www.baby2be.dk/field_responder.php",
							method: "post",
							dataType: "json",
							data: {action: "process_location", changed_item : $input.parents("tr:eq(0)").attr("class") , value : $input.val(), param : param},
							success: function(result){
							 	handler.ajax_receiver(result);
							}
						});
			}
		',
        'ajax_receiver' => '
			function(result){
				var handler = this;

				$.each(result.hide_items,function(i,item){
					handler.$parent_node.find("tr."+item).css("display","none");
					handler.$parent_node.find("tr."+item+" td.value").empty();
				});

				$.each(result.assign,function(item,html){
					var $node = handler.$parent_node.find("tr."+item+" td.value");
					$node.empty();
					$node.append(html);


					switch(item){
					case "state_id":
						$node.find("select[name=state_id]").change(function(){
						    handler.$errorContainer.empty();
							handler.change_event($(this),handler.fields.country_id);
						});
						break;
					case "zip":
						$node.find("input[type=button]").click(function(){
						    handler.$errorContainer.empty();
							handler.change_event($node.find("input[type=text]"),handler.fields.country_id);
						});
						break;

					case "city_id":
						handler.suggest($node.find("input[name=city_name]"),handler.fields.state_id);
						break;
					}

					$node.find("input, select").each(function(){
						var name = $(this).attr("name");
						if (name!=undefined){
							$(this).attr("name","location["+name+"]");
						}
					});
				});

				$.each(result.show_items,function(i,item){
					//this.$parent_node.find("tr."+result.show_items[item]).css("display","table-row");
					handler.$parent_node.find("tr."+item).fadeIn("slow");
				});


			}

		',
        '$' => 'function($expr){
			return this.$container_node.find($expr);
		}',
        '$suggest_cont_prototype' => 'undefined',
        'showSuggest' => 'function($node, suggests){
			var handler = this;

			var removeSuggest = function(){
				$node.parent().siblings(".suggest_cont").remove();
			}

			if (this.$suggest_cont_prototype ==undefined) {
				this.$suggest_cont_prototype = this.$(".suggest_cont").remove().clone();
			}

			var $suggest_cont = this.$suggest_cont_prototype.clone();

			removeSuggest();

			if (suggests.length <= 0) {
				return;
			}

			var itemHover = function($item){
				$item.parent().find(".suggest_item").removeClass("hover");
				$item.addClass("hover");
			}

			$.each(suggests, function(i, item){

				var $item_node = $suggest_cont.find(".prototype_node").clone().removeClass("prototype_node").css("display","block");
				var $parent_node = $suggest_cont.find(".prototype_node").parent();

				$item_node.html(item.suggest_label);

				$item_node.mouseover(function(){
					itemHover($item_node);
				});

				$item_node.click(function(){
					$node.val(item.name);
					removeSuggest();
					$node.focus();
				});

				$parent_node.append($item_node);

			});

			$node.unbind("keypress");
			$node.keypress(function(eventObject){

				$selected_item = $node.parent().find("div.suggest_cont ul li.hover");
				if ( $selected_item.length == 0 ) {
					$selected_item = $node.parent().find("div.suggest_cont ul li:visible:eq(0)");
					itemHover($selected_item);
					return;
				}

				switch(eventObject.keyCode){
					case 40:
						itemHover($selected_item.next(".suggest_item"));
						break;
					case 38:
						itemHover($selected_item.prev(".suggest_item"));
						break
					case 13:
						itemHover($selected_item.prev(".suggest_item"));
						if ($selected_item.length > 0) {
							$selected_item.click();
							return false;
						}
						break
				}
			});

			$node.unbind("blur");
			$node.blur(function(){
				window.setTimeout(removeSuggest,200);
			});

			$node.parent().after($suggest_cont);
			$suggest_cont.css("width",$node.outerWidth()).show();

		}',
        'suggest' => 'function($node, state_id){
			var handler = this;
			var timeout;
			var last_str;


			$node.unbind();
			$node.keyup(function(eventObject){

				var $field = $(this);

				var getCityList = function(str, state_id) {
					if (!$.trim(str)) {
						last_str = "";
						handler.showSuggest($node, []);
						return;
					}

					var key = eventObject.which;
					if ( last_str == str || key==13) {
						return;
					}

					var params ={
						str : str,
						state_id: state_id,
						action: "location_get_city"
					};
					$.ajax({
								url: "http://www.baby2be.dk/field_responder.php",
								method: "post",
								dataType: "json",
								data: params,
								success: function(result){
									last_str = str;
									handler.showSuggest($node, result, eventObject);
								}
						});
				}

				var suggestGetList = function(){
					var str = $field.val();
					getCityList(str, state_id);
				}

				if (timeout != undefined) {
					window.clearTimeout(timeout)
				}
				timeout = window.setTimeout(suggestGetList,300);
				return false;
			});
		}',
      ),
       'profile_field_id' => '197',
    )),
    'photo_upload' => 
    field_join_photo::__set_state(array(
       'owner_form_name' => 'profile_join',
       'default_allowed_extensions' => 
      array (
        0 => 'avi',
        1 => 'mpeg',
        2 => 'wmv',
        3 => 'flv',
        4 => 'mov',
        5 => 'mp4',
        6 => 'jpg',
        7 => 'jpeg',
        8 => 'gif',
        9 => 'png',
        10 => 'mp3',
      ),
       'allowed_extensions' => 
      array (
        0 => 'jpg',
        1 => 'jpeg',
        2 => 'gif',
        3 => 'png',
      ),
       'allowed_mime_types' => 
      array (
      ),
       'max_file_size' => 4194304,
       'multifile' => false,
       'max_files_num' => 0,
       'name' => 'photo_upload',
       'class' => 'field_join_photo',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler)
				{
					this.$input = $input;
					this.form_handler = form_handler;
					this.$iframe = $input.next();
					
					this.upload_error = false;
					
					
					
                    this.onConstruct($input, form_handler);
                    					
					var form_window = this.$iframe.get(0).contentWindow;
					
					var handler = this;
					
					var onload_ping =
						window.setInterval(function() {
							if (form_window.$userfile) {
								window.clearInterval(onload_ping);
								form_window.$userfile.change(function() {
									handler.uploadStart(form_window.$upload_form);
								});
							}
						}, 500);
				}',
        'validate' => 'function( value ) {}',
        'onConstruct' => 'function( $input, form_handler ) {}',
        'displayLoading' => 'function( value ) {
					this.$input.before(\'<img src="http://www.baby2be.dk/layout/img/loading.gif" />\');
				}',
        'focus' => 'function() {
					this.$iframe.get(0).contentWindow.$userfile.focus();
				}',
        'showLoading' => 'function() {
					this.$input.before(\'<img src="http://www.baby2be.dk/layout/img/loading.gif" />\');
				}',
        'hideLoading' => 'function() {
					this.$input.prev().remove();
				}',
        'uploadStart' => 'function($form)
				{
				    if ( !$form )
				    {
				        return;
				    }
				
					this.form_handler.file_upload_in_process = true;
					
					this.$iframe.hide();
					this.showLoading();
					
					var form_window = this.$iframe.get(0).contentWindow;
					form_window.$upload_form = null;
					form_window.$userfile = null;
					form_window = null;
					
 				    $form.submit();
					var handler = this;
					
					var ping = window.setInterval(function() {
						var form_window = handler.$iframe.get(0).contentWindow;
						if (form_window && form_window.$userfile)
						{
							window.clearInterval(ping);
							
							handler.hideLoading();
							
							if (form_window.sk_upload_error) {
							    handler.form_handler.clearErrors("photo_upload");
								handler.form_handler.error(form_window.sk_upload_error, "photo_upload");
								handler.$iframe.show();
								handler.construct(handler.$input, handler.form_handler);
								handler.upload_error = true;
							}
							else {
								var $input_clone;
								
								$("#" + handler.form_handler.auto_id + "-photo_upload-container").hide();
								
								if (!handler.$input.val()) {
									handler.$input.val(form_window.sk_userfile_uniqid);
								}
								else {
									$input_clone = handler.$input.before(
										handler.$input.clone()
											.val(form_window.sk_userfile_uniqid)
									);
								}
								
								var $preview = $(form_window.sk_userfile_preview);
								
								handler.$input.parent().find(".preview_cont").append($preview);
								
								$(".delete_file_btn", $preview)
									.one("click", function()
									{
										if (!$input_clone) {
											handler.$input.removeAttr("value");
										}
										else {
											$input_clone.remove();
										}
										
										if (handler.$iframe.css("display") == "none") {
											handler.$iframe.show();
											handler.construct(handler.$input, handler.form_handler);
										}
										
										if (typeof handler.free_file_slots != "undefined") {
											handler.free_file_slots++;
										}
										
										$preview.remove();
									});
							}
							
							handler.form_handler.file_upload_in_process = false;
							
							handler.uploadComplete();
						}
					}, 500);
				}',
        'uploadComplete' => 'function() {}',
      ),
       'profile_field_id' => '186',
    )),
    'general_description' => 
    fieldType_textarea::__set_state(array(
       'maxlength' => 2000,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 'general_description',
       'class' => 'fieldType_textarea',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '71',
    )),
    'religion' => 
    fieldType_select::__set_state(array(
       'values' => 
      array (
        0 => '1',
        1 => '2',
        2 => '4',
        3 => '8',
        4 => '16',
        5 => '32',
        6 => '64',
        7 => '128',
        8 => '256',
        9 => '1024',
        10 => '2048',
        11 => '4096',
        12 => '8192',
        13 => '16384',
        14 => '32768',
        15 => '65536',
        16 => '131072',
        17 => '262144',
        18 => '524288',
        19 => '1048576',
        20 => '2097152',
        21 => '4194304',
        22 => '8388608',
        23 => '16777216',
        24 => '33554432',
        25 => '67108864',
      ),
       'type' => 'select',
       'label_prefix' => 'religion',
       'column_size' => '33%',
       'name' => 'religion',
       'class' => 'fieldType_select',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '126',
    )),
    'password' => 
    fieldType_password::__set_state(array(
       'maxlength' => 40,
       'minlength' => 0,
       'regex' => 
      array (
        0 => '/^(.){4,30}$/i',
        1 => NULL,
      ),
       'size' => NULL,
       'name' => 'password',
       'class' => 'fieldType_password',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '3',
    )),
    're_password' => 
    fieldType_password::__set_state(array(
       'maxlength' => 255,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 're_password',
       'class' => 'fieldType_password',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
      ),
    )),
    'have_children' => 
    fieldType_select::__set_state(array(
       'values' => 
      array (
        0 => '1',
        1 => '2',
        2 => '4',
        3 => '8',
        4 => '16',
        5 => '32',
      ),
       'type' => 'select',
       'label_prefix' => 'have_children',
       'column_size' => '15%',
       'name' => 'have_children',
       'class' => 'fieldType_select',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '135',
    )),
    'body_type' => 
    fieldType_select::__set_state(array(
       'values' => 
      array (
        0 => '1',
        1 => '2',
        2 => '4',
        3 => '8',
        4 => '16',
        5 => '32',
      ),
       'type' => 'select',
       'label_prefix' => 'body_type',
       'column_size' => '50%',
       'name' => 'body_type',
       'class' => 'fieldType_select',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '122',
    )),
    'sex' => 
    fieldType_select::__set_state(array(
       'values' => 
      array (
        0 => '1',
        1 => '2',
        2 => '4',
      ),
       'type' => 'radio',
       'label_prefix' => 'sex',
       'column_size' => '50%',
       'name' => 'sex',
       'class' => 'fieldType_select',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '6',
    )),
    'eye_color' => 
    fieldType_select::__set_state(array(
       'values' => 
      array (
        0 => '1',
        1 => '2',
        2 => '4',
        3 => '8',
        4 => '16',
      ),
       'type' => 'select',
       'label_prefix' => 'eye_color',
       'column_size' => '50%',
       'name' => 'eye_color',
       'class' => 'fieldType_select',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '130',
    )),
    'match_sex' => 
    fieldType_set::__set_state(array(
       'values' => 
      array (
        0 => '1',
        1 => '2',
        2 => '4',
      ),
       'type' => 'multicheckbox',
       'column_size' => '50%',
       'label_prefix' => 'sex',
       'name' => 'match_sex',
       'class' => 'fieldType_set',
       'value' => 
      array (
      ),
       'js_presentation' => 
      array (
        'construct' => '
			function($input, formHandler, auto_id){
				var handler = this;

                var $node = $("input[name=\'match_sex_select_all\']");
                this.lastStatus = $node.prop("checked");

                $("input[name=\'match_sex\\[\\]\']").click(function(){
                    if ($node.prop("checked"))
                    {
                        $node.removeAttr("checked");
                        handler.lastStatus = false;
                    }
                });

				$node.click(function(){

                        if (handler.lastStatus)
                        {
                            $.each($("#"+auto_id+" li"), function(){
                                $(this).find("input").removeAttr("checked");
                            });
                            handler.lastStatus = false;
                        }
                        else
                        {
                            $.each($("#"+auto_id+" li"), function(){
                                $(this).find("input").prop("checked", "checked");
                            });
                            handler.lastStatus = true;
                        }
				});
			}
		',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
        '$prototype_node' => '{}',
      ),
       'profile_field_id' => '68',
    )),
    'email' => 
    field_join_email::__set_state(array(
       'maxlength' => 128,
       'minlength' => 0,
       'regex' => 
      array (
        0 => '/^[a-zA-Z0-9_\\-\\.]+@([a-zA-Z0-9_\\-]+\\.)+?[a-zA-Z0-9_]{2,}(\\.\\w{2})?$/i',
        1 => NULL,
      ),
       'size' => NULL,
       'name' => 'email',
       'class' => 'field_join_email',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => '
					function($input) {
						var handler = this;
						
						this.lastValue = $input.parent().find("input[name=last_value]").val();
						
						if($input.val()!="" && $input.val()!=this.lastValue){
							handler.check_email($input);
						}		
						
						$input.change(function(){
							handler.changed = true;
						});
										
						$input.blur(function() {
							if($input.val()!="" && $input.val()!=handler.lastValue && handler.changed ){
								handler.check_email($input);
							}
							else{
								$input.parent().find(".success").remove();
							}						
						});
					}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
        'lastValue' => '""',
        'changed' => 'true',
        'check_email' => '
					function($input){
					var handler = this;
					
						$.ajax({
								url: "http://www.baby2be.dk/field_responder.php",
								method: "post",
								dataType: "json",
								data: {action: "check_email_exists", email: $input.val()},
								success: function(result){
										$input.parent().find(".success").remove();
										handler.changed = false;
                                        if(result==1){
   											$input.after(\'<span class="success">&nbsp;</span>\');
   										}
                                        else{
                                            SK_drawError(result);											
   										}
  								}
							});
					}
					',
      ),
       'profile_field_id' => '1',
    )),
    're_email' => 
    fieldType_text::__set_state(array(
       'maxlength' => 255,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 're_email',
       'class' => 'fieldType_text',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
      ),
    )),
    'birthdate' => 
    field_birthDate::__set_state(array(
       'invite_messages' => 
      array (
      ),
       'range' => 
      array (
        'min' => '1950',
        'max' => '1993',
      ),
       'invitePrefix' => '%profile_fields.select_invite_msg.8_',
       'order' => 'desc',
       'name' => 'birthdate',
       'class' => 'field_birthDate',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {
			if ( !required ) {
				return;
			}
			if ( !$.trim(value.year) ) {
				throw new SK_FormFieldValidationException("");
			}

			if ( !$.trim(value.month) ) {
				throw new SK_FormFieldValidationException("");
			}

			if ( !$.trim(value.day) ) {
				throw new SK_FormFieldValidationException("");
			}

		}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '8',
    )),
    'match_agerange' => 
    fieldType_age_range::__set_state(array(
       'full_range' => 
      array (
        0 => 20,
        1 => 63,
      ),
       'order' => 'asc',
       'name' => 'match_agerange',
       'class' => 'fieldType_age_range',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '9',
    )),
    'interests' => 
    fieldType_set::__set_state(array(
       'values' => 
      array (
        0 => '1',
        1 => '2',
        2 => '4',
        3 => '8',
        4 => '16',
        5 => '32',
        6 => '64',
        7 => '2048',
        8 => '4096',
        9 => '16384',
        10 => '32768',
      ),
       'type' => 'multicheckbox',
       'column_size' => '50%',
       'label_prefix' => 'interests',
       'name' => 'interests',
       'class' => 'fieldType_set',
       'value' => 
      array (
      ),
       'js_presentation' => 
      array (
        'construct' => '
			function($input, formHandler, auto_id){
				var handler = this;

                var $node = $("input[name=\'interests_select_all\']");
                this.lastStatus = $node.prop("checked");

                $("input[name=\'interests\\[\\]\']").click(function(){
                    if ($node.prop("checked"))
                    {
                        $node.removeAttr("checked");
                        handler.lastStatus = false;
                    }
                });

				$node.click(function(){

                        if (handler.lastStatus)
                        {
                            $.each($("#"+auto_id+" li"), function(){
                                $(this).find("input").removeAttr("checked");
                            });
                            handler.lastStatus = false;
                        }
                        else
                        {
                            $.each($("#"+auto_id+" li"), function(){
                                $(this).find("input").prop("checked", "checked");
                            });
                            handler.lastStatus = true;
                        }
				});
			}
		',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
        '$prototype_node' => '{}',
      ),
       'profile_field_id' => '148',
    )),
    'i_am_at_least_18_years_old' => 
    fieldType_checkbox::__set_state(array(
       'name' => 'i_am_at_least_18_years_old',
       'class' => 'fieldType_checkbox',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '164',
    )),
    'i_agree_with_tos' => 
    fieldType_checkbox::__set_state(array(
       'name' => 'i_agree_with_tos',
       'class' => 'fieldType_checkbox',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '188',
    )),
    'real_name' => 
    fieldType_text::__set_state(array(
       'maxlength' => 128,
       'minlength' => 0,
       'regex' => 
      array (
        0 => '/^[a-zA-Z0-9\\s]{2,}$/',
        1 => NULL,
      ),
       'size' => NULL,
       'name' => 'real_name',
       'class' => 'fieldType_text',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '124',
    )),
    'headline' => 
    fieldType_text::__set_state(array(
       'maxlength' => 128,
       'minlength' => 0,
       'regex' => NULL,
       'size' => NULL,
       'name' => 'headline',
       'class' => 'fieldType_text',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required, autoId) {
	       if ( required && $("#" + autoId).hasClass("input_invitation") )
	       {
	           throw new Exception();
	       }
	    }',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '102',
    )),
    'relationship' => 
    fieldType_set::__set_state(array(
       'values' => 
      array (
        0 => '1',
        1 => '2',
        2 => '4',
        3 => '8',
        4 => '16',
        5 => '32',
        6 => '64',
      ),
       'type' => 'multicheckbox',
       'column_size' => '50%',
       'label_prefix' => 'relationship',
       'name' => 'relationship',
       'class' => 'fieldType_set',
       'value' => 
      array (
      ),
       'js_presentation' => 
      array (
        'construct' => '
			function($input, formHandler, auto_id){
				var handler = this;

                var $node = $("input[name=\'relationship_select_all\']");
                this.lastStatus = $node.prop("checked");

                $("input[name=\'relationship\\[\\]\']").click(function(){
                    if ($node.prop("checked"))
                    {
                        $node.removeAttr("checked");
                        handler.lastStatus = false;
                    }
                });

				$node.click(function(){

                        if (handler.lastStatus)
                        {
                            $.each($("#"+auto_id+" li"), function(){
                                $(this).find("input").removeAttr("checked");
                            });
                            handler.lastStatus = false;
                        }
                        else
                        {
                            $.each($("#"+auto_id+" li"), function(){
                                $(this).find("input").prop("checked", "checked");
                            });
                            handler.lastStatus = true;
                        }
				});
			}
		',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
        '$prototype_node' => '{}',
      ),
       'profile_field_id' => '10',
    )),
  ),
   'hidden_fields' => 
  array (
  ),
   'actions' => 
  array (
    'action_1___' => 
    formAction_JoinProfile::__set_state(array(
       'step' => '1',
       'reliant_field_value' => '',
       'uniqid' => '1___',
       'name' => 'action_1___',
       'class' => 'formAction_JoinProfile',
       'fields' => 
      array (
        'captcha' => true,
        'username' => true,
        'location' => true,
        'photo_upload' => true,
        'password' => true,
        're_password' => true,
        'sex' => true,
        'match_sex' => true,
        'email' => true,
        're_email' => true,
        'birthdate' => true,
        'match_agerange' => false,
        'interests' => false,
        'i_am_at_least_18_years_old' => true,
        'i_agree_with_tos' => true,
      ),
       'process_fields' => 
      array (
        0 => 'captcha',
        1 => 'username',
        2 => 'location',
        3 => 'photo_upload',
        4 => 'password',
        5 => 're_password',
        6 => 'sex',
        7 => 'match_sex',
        8 => 'email',
        9 => 're_email',
        10 => 'birthdate',
        11 => 'match_agerange',
        12 => 'interests',
        13 => 'i_am_at_least_18_years_old',
        14 => 'i_agree_with_tos',
      ),
       'required_fields' => 
      array (
        0 => 'captcha',
        1 => 'username',
        2 => 'location',
        3 => 'photo_upload',
        4 => 'password',
        5 => 're_password',
        6 => 'sex',
        7 => 'match_sex',
        8 => 'email',
        9 => 're_email',
        10 => 'birthdate',
        11 => 'i_am_at_least_18_years_old',
        12 => 'i_agree_with_tos',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
    'action_1___1' => 
    formAction_JoinProfile::__set_state(array(
       'step' => '1',
       'reliant_field_value' => '1',
       'uniqid' => '1___1',
       'name' => 'action_1___1',
       'class' => 'formAction_JoinProfile',
       'fields' => 
      array (
        'captcha' => true,
        'username' => true,
        'location' => true,
        'photo_upload' => true,
        'password' => true,
        're_password' => true,
        'sex' => true,
        'match_sex' => true,
        'email' => true,
        're_email' => true,
        'birthdate' => true,
        'match_agerange' => false,
        'interests' => false,
        'i_am_at_least_18_years_old' => true,
        'i_agree_with_tos' => true,
      ),
       'process_fields' => 
      array (
        0 => 'captcha',
        1 => 'username',
        2 => 'location',
        3 => 'photo_upload',
        4 => 'password',
        5 => 're_password',
        6 => 'sex',
        7 => 'match_sex',
        8 => 'email',
        9 => 're_email',
        10 => 'birthdate',
        11 => 'match_agerange',
        12 => 'interests',
        13 => 'i_am_at_least_18_years_old',
        14 => 'i_agree_with_tos',
      ),
       'required_fields' => 
      array (
        0 => 'captcha',
        1 => 'username',
        2 => 'location',
        3 => 'photo_upload',
        4 => 'password',
        5 => 're_password',
        6 => 'sex',
        7 => 'match_sex',
        8 => 'email',
        9 => 're_email',
        10 => 'birthdate',
        11 => 'i_am_at_least_18_years_old',
        12 => 'i_agree_with_tos',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
    'action_2___1' => 
    formAction_JoinProfile::__set_state(array(
       'step' => '2',
       'reliant_field_value' => '1',
       'uniqid' => '2___1',
       'name' => 'action_2___1',
       'class' => 'formAction_JoinProfile',
       'fields' => 
      array (
        'occupation' => false,
        'general_description' => true,
        'religion' => false,
        'have_children' => false,
        'body_type' => false,
        'real_name' => true,
        'headline' => true,
        'relationship' => true,
      ),
       'process_fields' => 
      array (
        0 => 'occupation',
        1 => 'general_description',
        2 => 'religion',
        3 => 'have_children',
        4 => 'body_type',
        5 => 'real_name',
        6 => 'headline',
        7 => 'relationship',
      ),
       'required_fields' => 
      array (
        0 => 'general_description',
        1 => 'real_name',
        2 => 'headline',
        3 => 'relationship',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
    'action_1___2' => 
    formAction_JoinProfile::__set_state(array(
       'step' => '1',
       'reliant_field_value' => '2',
       'uniqid' => '1___2',
       'name' => 'action_1___2',
       'class' => 'formAction_JoinProfile',
       'fields' => 
      array (
        'captcha' => true,
        'username' => true,
        'location' => true,
        'photo_upload' => true,
        'password' => true,
        're_password' => true,
        'sex' => true,
        'match_sex' => true,
        'email' => true,
        're_email' => true,
        'birthdate' => true,
        'match_agerange' => false,
        'interests' => false,
        'i_am_at_least_18_years_old' => true,
        'i_agree_with_tos' => true,
      ),
       'process_fields' => 
      array (
        0 => 'captcha',
        1 => 'username',
        2 => 'location',
        3 => 'photo_upload',
        4 => 'password',
        5 => 're_password',
        6 => 'sex',
        7 => 'match_sex',
        8 => 'email',
        9 => 're_email',
        10 => 'birthdate',
        11 => 'match_agerange',
        12 => 'interests',
        13 => 'i_am_at_least_18_years_old',
        14 => 'i_agree_with_tos',
      ),
       'required_fields' => 
      array (
        0 => 'captcha',
        1 => 'username',
        2 => 'location',
        3 => 'photo_upload',
        4 => 'password',
        5 => 're_password',
        6 => 'sex',
        7 => 'match_sex',
        8 => 'email',
        9 => 're_email',
        10 => 'birthdate',
        11 => 'i_am_at_least_18_years_old',
        12 => 'i_agree_with_tos',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
    'action_2___2' => 
    formAction_JoinProfile::__set_state(array(
       'step' => '2',
       'reliant_field_value' => '2',
       'uniqid' => '2___2',
       'name' => 'action_2___2',
       'class' => 'formAction_JoinProfile',
       'fields' => 
      array (
        'occupation' => false,
        'general_description' => true,
        'religion' => false,
        'have_children' => false,
        'body_type' => false,
        'real_name' => true,
        'headline' => true,
        'relationship' => true,
      ),
       'process_fields' => 
      array (
        0 => 'occupation',
        1 => 'general_description',
        2 => 'religion',
        3 => 'have_children',
        4 => 'body_type',
        5 => 'real_name',
        6 => 'headline',
        7 => 'relationship',
      ),
       'required_fields' => 
      array (
        0 => 'general_description',
        1 => 'real_name',
        2 => 'headline',
        3 => 'relationship',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
    'action_1___4' => 
    formAction_JoinProfile::__set_state(array(
       'step' => '1',
       'reliant_field_value' => '4',
       'uniqid' => '1___4',
       'name' => 'action_1___4',
       'class' => 'formAction_JoinProfile',
       'fields' => 
      array (
        'captcha' => true,
        'username' => true,
        'location' => true,
        'photo_upload' => true,
        'password' => true,
        're_password' => true,
        'sex' => true,
        'match_sex' => true,
        'email' => true,
        're_email' => true,
        'birthdate' => true,
        'match_agerange' => false,
        'interests' => false,
        'i_am_at_least_18_years_old' => true,
        'i_agree_with_tos' => true,
      ),
       'process_fields' => 
      array (
        0 => 'captcha',
        1 => 'username',
        2 => 'location',
        3 => 'photo_upload',
        4 => 'password',
        5 => 're_password',
        6 => 'sex',
        7 => 'match_sex',
        8 => 'email',
        9 => 're_email',
        10 => 'birthdate',
        11 => 'match_agerange',
        12 => 'interests',
        13 => 'i_am_at_least_18_years_old',
        14 => 'i_agree_with_tos',
      ),
       'required_fields' => 
      array (
        0 => 'captcha',
        1 => 'username',
        2 => 'location',
        3 => 'photo_upload',
        4 => 'password',
        5 => 're_password',
        6 => 'sex',
        7 => 'match_sex',
        8 => 'email',
        9 => 're_email',
        10 => 'birthdate',
        11 => 'i_am_at_least_18_years_old',
        12 => 'i_agree_with_tos',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
    'action_2___4' => 
    formAction_JoinProfile::__set_state(array(
       'step' => '2',
       'reliant_field_value' => '4',
       'uniqid' => '2___4',
       'name' => 'action_2___4',
       'class' => 'formAction_JoinProfile',
       'fields' => 
      array (
        'occupation' => false,
        'general_description' => true,
        'religion' => false,
        'have_children' => false,
        'body_type' => false,
        'eye_color' => false,
        'real_name' => true,
        'headline' => true,
        'relationship' => true,
      ),
       'process_fields' => 
      array (
        0 => 'occupation',
        1 => 'general_description',
        2 => 'religion',
        3 => 'have_children',
        4 => 'body_type',
        5 => 'eye_color',
        6 => 'real_name',
        7 => 'headline',
        8 => 'relationship',
      ),
       'required_fields' => 
      array (
        0 => 'general_description',
        1 => 'real_name',
        2 => 'headline',
        3 => 'relationship',
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
  ),
   'default_action' => '',
   'frontend_data' => 
  array (
    'js_class' => 'form_Join',
    'js_file' => 'http://www.baby2be.dk/external_c/gh/%25%251C/1C7/1C7273FE%25%25form_Join.js',
  ),
   'frontend_handler' => NULL,
));
?>