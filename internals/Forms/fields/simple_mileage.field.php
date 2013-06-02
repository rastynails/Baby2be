<?php

class field_simple_mileage extends SK_FormField
{
	public $by_zip = true;

	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'mileage', $by_zip = true ) {
		parent::__construct($name);
		$this->by_zip = $by_zip;
	}

	public function setup( SK_Form $Form )
	{
		if ($this->by_zip) {
			return parent::setup($Form);
		}

		$this->js_presentation['$container_node'] = '{}';

		$this->js_presentation['construct'] = 'function($input){
			if ($input!=undefined) {
				this.$container_node = $input.parents(".field_mileage:eq(0)");
			}

			var handler = this;

			this.prepareTabs();
            this.$container_node.find("select[name*=country_id]").change();
		}';

		$this->js_presentation['$'] = 'function($expr){
			return this.$container_node.find($expr);
		}';


		$this->js_presentation['ajaxCall'] = 'function(params, callback){
			var handler = this;
			params.action = "process_mileage";
			$.ajax({
						url: "'.URL_FIELD_RESPONDER.'",
						method: "post",
						dataType: "json",
						data: params,
						success: function(result){
						 	callback(result);
						}
				});
		}';

		$this->js_presentation['prepareTabs'] = 'function(){
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
							$field.append("<option value=\"\">" + SK_Language.text("%profile_fields.select_invite_msg.' . SK_ProfileFields::get('state_id')->profile_field_id . '") + "</option>");
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
		}';

		$this->js_presentation['$suggest_cont_prototype'] = 'undefined';

		$this->js_presentation['showSuggest'] = 'function($node, suggests){
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

		}';

		$this->js_presentation['suggest'] = 'function($node, state_id){
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
								url: "'.URL_FIELD_RESPONDER.'",
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
		}';

		$this->js_presentation['fillSelect'] = 'function($select, data){
			$.each(data,function(i, item){
				var $option = $("<option>" + item.name + "</option>");
				$option.attr("value", item.value);
				$select.append($option);
			});
		}';

		parent::setup($Form);
	}

	public static function ajax_handler($params)
	{
		if ( ( isset($params['country_id']) && strlen($country_id = trim($params['country_id'])) )
				&& !isset($params['state_id'])) {
			$states = app_Location::getStaties($country_id);
			$out = array();
			foreach ($states as $item){
				$out[] = array('name'=>$item['Admin1_str_name'],'value'=>$item['Admin1_str_code']);
			}
			return $out;
		}

		if (( isset($params['country_id']) && ($country_id = trim($params['country_id'])) )
			&& (isset($params['state_id'])&&($state_id = trim($params['state_id'])))) {

			return array('state_id'=>app_Location::isStateCityExists($state_id) ? $state_id : '');
		}

		if ( isset($params['region_id']) && ($region_id = trim($params['region_id'])) ) {
			return app_Location::getRegionCountries($region_id);
		}
		return array();
	}


	public function validate( $value )
	{
		return $value;
	}


	public function render( array $params = null , SK_Form $form = null)
	{
		$Frontend = SK_Layout::frontend_handler();
		$Frontend->registerLanguageValue("%profile_fields.select_invite_msg.".SK_ProfileFields::get('state_id')->profile_field_id);

		$field_name = $this->getName();
		$lang = SK_Language::section('forms._fields.mileage');
		$value = $this->getValue();
		$db_search_unit = SK_Config::section('site')->Section('additional')->Section('profile_list')->search_distance_unit;
		$search_unit = SK_Language::text('i18n.location.' . $db_search_unit);
		if ($this->by_zip) {

			return
<<<EOT
		<div class="center">
			<input type="text" size="4" name="{$field_name}[radius_third]" value="{$value["radius_third"]}">&nbsp;{$lang->text('miles_from', array('unit' => $search_unit))}&nbsp;
			<input type="text" size="9" name="{$field_name}[zip_third]" value="{$value["zip_third"]}">&nbsp;{$lang->text('zip')}
		</div>

EOT;

		}

		$lang_section = SK_Language::section("profile_fields.label_search");

		$langs["country"] = $lang_section->text("112");
		$langs["state"] = $lang_section->text("113");
		$langs["city"] = $lang_section->text("114");
		$langs["custom_location"] = $lang_section->text("56");



		$countries = app_Location::Countries();

		$countries_out = '<select name="'.$field_name.'[country_id]">';
		$countries_out .='<option value="">'
						.SK_Language::text("%profile_fields.select_invite_msg.".SK_ProfileFields::get('country_id')->profile_field_id)
						.'</option>';
        if (count($countries)>1)
        {
            foreach ($countries as $item)
            {
                $selected = (trim($value['country_id'])==$item["Country_str_code"]) ? 'selected="selected"' : '';

                $countries_out.='<option value="'.$item["Country_str_code"].'" '.$selected.'>'.$item["Country_str_name"].'</option>';
            }
        }
        else
        {
            if (!empty($value['country_id']))
            {
                $selected = (trim($value['country_id'])==$countries[0]["Country_str_code"]) ? 'selected="selected"' : '';
            }
            else
            {
                $selected = 'selected="selected"';
            }

            $countries_out.='<option value="'.$countries[0]["Country_str_code"].'" '.$selected.'>'.$countries[0]["Country_str_name"].'</option>';
        }
		$countries_out.= '</select>';

		$states_value =  isset($value["state_id"]) ? '<option value="' . trim($value["state_id"]) . '" selected="selected"></option>' : '';

		$city_value = (bool)((int)$value["city_id"]) ? app_Location::CityNameById($value["city_id"]) : $value["city_id"];

		$output =
<<<EOT

<div class="field_mileage">
	<div class="content_container">
		<table>
			<tbody>
				<tr>
					<td width="40%" align="right">
						{$langs["country"]}
					</td>
					<td width="60%" class="value">
						$countries_out
					</td>
				</tr>
				<tr style="display:none">
					<td align="right">
						{$langs["state"]}
					</td>
					<td class="value">
						<select name="{$field_name}[state_id]">
							$states_value
						</select>
					</td>
				</tr>
				<tr style="display:none">
					<td align="right">
						<div class="radius_first"><input type="text" size="4" name="{$field_name}[radius_first]" value="{$value["radius_first"]}">
						{$lang->text('miles_from', array('unit' => $search_unit))}</div>
					</td>
					<td class="value" valign="top">
						<div class="city_name">
							<input type="text" name="{$field_name}[city_id]" value="{$city_value}" autocomplete=off > {$langs["city"]}
						</div>
					</td>
				</tr>
				<tr style="display:none">
					<td align="right">
						{$langs["custom_location"]}
					</td>
					<td class="value">
						<input type="text" name="{$field_name}[custom_location]" value="{$value["custom_location"]}">
					</td>
				</tr>
			</tbody>
		</table>


	</div>

	<div class="suggest_cont" style="display:none">
		<ul>
			<li class="suggest_item prototype_node" style="display:none"></li>
		</ul>
	</div>
</div>
EOT;

		return $output;
	}

}
