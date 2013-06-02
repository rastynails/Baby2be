<?php
$form = form_QuickSearch::__set_state(array(
   'search_type' => 'quick_search',
   'search_list_id' => NULL,
   'search_criterion' => NULL,
   'class' => 'form_QuickSearch',
   'name' => 'quick_search',
   'fields' => 
  array (
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
    'birthdate' => 
    fieldType_age_range::__set_state(array(
       'full_range' => 
      array (
        0 => 20,
        1 => 63,
      ),
       'order' => 'asc',
       'name' => 'birthdate',
       'class' => 'fieldType_age_range',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '8',
    )),
    'location' => 
    field_simple_mileage::__set_state(array(
       'by_zip' => false,
       'name' => 'location',
       'class' => 'field_simple_mileage',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input){
			if ($input!=undefined) {
				this.$container_node = $input.parents(".field_mileage:eq(0)");
			}

			var handler = this;

			this.prepareTabs();
            this.$container_node.find("select[name*=country_id]").change();
		}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
        '$container_node' => '{}',
        '$' => 'function($expr){
			return this.$container_node.find($expr);
		}',
        'ajaxCall' => 'function(params, callback){
			var handler = this;
			params.action = "process_mileage";
			$.ajax({
						url: "http://www.matingdating.dk/field_responder.php",
						method: "post",
						dataType: "json",
						data: params,
						success: function(result){
						 	callback(result);
						}
				});
		}',
        'prepareTabs' => 'function(){
			var handler = this;
			var $tab1 = this.$container_node;
			var state_value = $tab1.find("select[name*=state_id]").val();
			var city_value = $tab1.find("input[name*=city_id]").val();
			var radius_first_value = $tab1.find("input[name*=radius_first]").val();
			var custom_location_value = $tab1.find("input[name*=custom_location]").val();


			$tab1.find("[name*=country_id]").change(function(){
					var $parent = $(this).parents(".content_container");

					$parent.find("[name*=state_id]").parents("tr:eq(0)").hide();
					$parent.find("[name*=city_id]").parents("tr:eq(0)").hide();
					$parent.find("[name*=city_id]").val("");
					$parent.find("[name*=radius_first]").val("");
					$parent.find("[name*=custom_location]").val("");
					$parent.find("[name*=custom_location]").parents("tr:eq(0)").hide();
					if ($(this).val()=="") return;
					handler.ajaxCall({country_id: $(this).val()},function(result){

						if (result.length > 0) {
							var $field = $parent.find("[name*=state_id]");


							$field.empty();
							$field.append("<option value=\\"\\">" + SK_Language.text("%profile_fields.select_invite_msg.113") + "</option>");
							$field.unbind();
							$field.parents("tr:eq(0)").show();
							handler.fillSelect($field,result);
							$parent.find("[name*=custom_location]").parents("tr:eq(0)").hide();

							$field.change(function(){

								handler.ajaxCall({country_id: $parent.find("[name*=country_id]").val(), state_id: $(this).val()}, function(result){

									$parent.find("[name*=city_id]").val("")
											.parents("tr:eq(0)").hide();
									$parent.find("[name*=radius_first]").val("");

									$parent.find("[name*=city_id]").parent().find(".suggest_cont").remove();

									if (result.state_id){

										if(city_value) {
											$parent.find("[name*=city_id]").val(city_value);
											city_value= false;
										}

										if(radius_first_value) {
											$parent.find("[name*=radius_first]").val(radius_first_value);
											radius_first_value= false;
										}
										else {
											$parent.find("[name*=radius_first]").val("");
										}

										$parent.find("[name*=city_id]").parents("tr:eq(0)").show();
										handler.suggest($parent.find("[name*=city_id]"),result.state_id);

									}

								});
							});

							if (state_value) {
								$field.find("option[value=" + state_value + "]").attr("selected", "selected");
								state_value = false;
								$field.change();
							}
						}
						else {

							if (custom_location_value) {
								$parent.find("[name*=custom_location]").val(custom_location_value);
								custom_location_value = false;
							}

							$parent.find("[name*=custom_location]").parents("tr:eq(0)").show();
							$parent.find("[name*=state_id]").parents("tr:eq(0)").hide();
						}
					});
			});
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
								url: "http://www.matingdating.dk/field_responder.php",
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
        'fillSelect' => 'function($select, data){
			$.each(data,function(i, item){
				var $option = $("<option>" + item.name + "</option>");
				$option.attr("value", item.value);
				$select.append($option);
			});
		}',
      ),
    )),
    'search_online_only' => 
    fieldType_checkbox::__set_state(array(
       'name' => 'search_online_only',
       'class' => 'fieldType_checkbox',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '20',
    )),
    'search_with_photo_only' => 
    fieldType_checkbox::__set_state(array(
       'name' => 'search_with_photo_only',
       'class' => 'fieldType_checkbox',
       'value' => NULL,
       'js_presentation' => 
      array (
        'construct' => 'function($input, form_handler) {}',
        'validate' => 'function(value, required) {}',
        'focus' => 'function() {}',
      ),
       'profile_field_id' => '21',
    )),
  ),
   'hidden_fields' => 
  array (
  ),
   'actions' => 
  array (
    'search' => 
    formAction_SearchProfile::__set_state(array(
       'form_name' => 'quick_search',
       'name' => 'search',
       'class' => 'formAction_SearchProfile',
       'fields' => 
      array (
        'sex' => false,
        'match_sex' => false,
        'birthdate' => false,
        'location' => false,
        'search_online_only' => false,
        'search_with_photo_only' => false,
      ),
       'process_fields' => 
      array (
        0 => 'sex',
        1 => 'match_sex',
        2 => 'birthdate',
        3 => 'location',
        4 => 'search_online_only',
        5 => 'search_with_photo_only',
      ),
       'required_fields' => 
      array (
      ),
       'confirm_msg_lang_addr' => NULL,
    )),
  ),
   'default_action' => '',
   'frontend_data' => 
  array (
    'js_class' => 'form_QuickSearch',
    'js_file' => 'http://www.matingdating.dk/external_c/gh/%25%2510/103/10379A4F%25%25form_QuickSearch.js',
  ),
   'frontend_handler' => NULL,
));
?>