<?php

class field_mileage extends SK_FormField
{
    public $oneCountry = false;

    public $noZips = false;
	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct( $name = 'mileage' ) {
		parent::__construct($name);

        $countries = app_Location::Countries();

        if ( !empty($countries) && count($countries) <=1 )
        {
            $this->oneCountry = true;
        }

		$_query = "SELECT count(*) FROM `".TBL_LOCATION_ZIP."` ";

		$count = MySQL::fetchField( $_query );

        if ( !empty($count)  )
        {
            $this->noZips = true;
        }

	}

	public function setup( SK_Form $Form )
	{
		$regions = app_Location::getRegionNames();


		$this->js_presentation['$container_node'] = '{}';

		$this->js_presentation['tab_prototype'] = '{}';

		$this->js_presentation['tabs'] = '{}';

		$this->js_presentation['active_tab'] = 'undefined';

		$this->js_presentation['construct'] = 'function($input){
			if ($input!=undefined) {
				this.$container_node = $input.parents(".field_mileage:eq(0)");
			}

			var handler = this;

			this.$tab_prototype = this.$(".tab_container .prototype_node")
				.clone()
				.removeClass("prototype_node");

			this.$(".tab_container .prototype_node").remove();


			this.prepareTabs();
		}';

		$this->js_presentation['$'] = 'function($expr){
			return this.$container_node.find($expr);
		}';

		$this->js_presentation['reset'] = 'function($node){
			$("input, select", $node).each(function(i, obj){
				if ($(obj).is("select")) {
					obj.selectedIndex = 0;
				} else if($(obj).is("input[type=checkbox]")) {
					$(obj).attr("checked", false);
				} else {
					$(obj).val("");
				}
			});
		}';

		$this->js_presentation['resetTab'] = 'function(tab){
			this.reset(tab["content"]);

			if (tab.name == "tab1") {
				var $cont = tab["content"];
				$cont.find("[name*=state_id]").parents("tr:eq(0)").hide();
				$cont.find("[name*=city_id]").parents("tr:eq(0)").hide();
				$cont.find("[name*=custom_location]").parents("tr:eq(0)").hide();
			} else if(tab.name == "tab2") {
				tab["content"].find(".country").hide();
			}
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


			var $tab1 = this.$(".tab_1");

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

			this.addTab("tab1", SK_Language.text("%forms._fields.mileage.tab1"),$tab1, $tab1.hasClass("active"));

			var $tab2 = this.$(".tab_2");

			var $region_cont_prototype = $tab2.find(".region_cont").clone();
			$tab2.find(".region_cont").remove();

			var regions = '.json_encode($regions).';

			$.each(regions, function(code, name){
				var $region_cont = $region_cont_prototype.clone();
				var $region = $region_cont.find(".region");
				$region.text(name);

				var $region_val_cont = $tab2.find(".tab_values .region_value");

				var active = ($region_val_cont.find(".region_id:contains(" + code + ")").length > 0);

				if (active) {
					$region.addClass("active");
				}


                                var ropen = function(){

					$region_cont = $(this).parent();
					var $country_list = $region_cont.find(".country_list").hide();
					if ( $country_list.find(".country:visible").length <= 0 )
					{
						handler.ajaxCall({region_id: code},function(result){

							var $country_prot = $country_list.find(".country").clone().removeClass("prototype_node");

							$.each(result, function(code, name){
								var active_country = ($region_val_cont.find(".country:contains("+code+")").length > 0);

								var $country = $country_prot.clone();

								if (active_country) {
									$country.find(":checkbox").attr("checked", true);
								}

								$country.find("label").text(name).attr("for",code);
								$country.find(":checkbox").attr("id",code).val(code);
								$country_list.append($country);
							});
							$country_list.slideDown("normal");

						});
					}
					else {
						$country_list.slideDown("normal");
					}


				};

                                var rclose = function(){
                                    $region_cont = $(this).parent();
                                    var $country_list = $region_cont.find(".country_list");
                                    $country_list.slideUp("normal");
                                };

                                var toggle = true;

                                $region.click(function(){
                                    if ( toggle ) {
                                        ropen.call(this);
                                        toggle = false;
                                    }
                                    else {
                                        rclose.call(this);
                                        toggle = true;
                                    }
                                });

				$tab2.find(".tab_content").append($region_cont);
			});

            if ( "'.$this->oneCountry.'" != "false" )
            {
                this.addTab("tab2", SK_Language.text("%forms._fields.mileage.tab2"), $tab2, $tab2.hasClass("active"));
            }

            if ( "'.$this->noZips.'" != "false" )
            {
                this.addTab("tab3",SK_Language.text("%forms._fields.mileage.tab3"), this.$(".tab_3"), this.$(".tab_3").hasClass("active"));
            }

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


		$this->js_presentation['activateTab'] = 'function(tab){
			var handler = this;
			var $cont = this.$(".content_container");

			if (this.active_tab!=undefined) {
				this.active_tab["tab"].removeClass("active");
			}
			tab["tab"].addClass("active");

			$cont.fadeOut("fast",function(){
				if (handler.active_tab!=undefined) {
					$cont.find(".tab_" + handler.active_tab["name"]).hide();
					handler.active_tab["content"] = $cont.find(".tab_" + handler.active_tab["name"]);
					handler.resetTab(handler.active_tab);
				}

				if ($cont.find(".tab_" + tab["name"]).length > 0)
				{
					$cont.find(".tab_" + tab["name"]).fadeIn("fast");
					$cont.fadeIn("fast");
				}
				else
				{
					var $active_content = tab["content"].find(".tab_content");
					$active_content.addClass("tab_" + tab["name"]);
					$cont.append($active_content).fadeIn("fast");

					$active_content.find("select[name*=country_id]").change();
				}
				handler.active_tab = tab;
			});

		}';


		$this->js_presentation['addTab'] = 'function(name, label, $content, active){
			var handler = this;
			this.tabs[name] = {
				name: name,
				tab: this.$tab_prototype.clone(),
				content: $content.clone(true),
				label: label
			};
			$content.remove();

			var $tab = this.tabs[name];
			var $tab_node = $tab["tab"]
			if (active) {
				handler.activateTab($tab);
			}

			$tab_node.click(function(){
				if (!$(this).hasClass("active")) {
					handler.activateTab($tab);
				}
			});
			$tab_node.text(label);
			$tab_node.appendTo(this.$(".tab_container"));
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
		$_value = null;
		foreach ($value as $key => $item) {
			if (is_array($item)) {
				foreach ($item as $_key => $_item) {
					if (!$_item) {
						continue;
					}
				}
			} elseif(!$item) {
				continue;
			}
			$_value[$key] = $item;
		}

		return $_value;
	}


	public function render( array $params = null, SK_Form $form = null )
	{
		$Frontend = SK_Layout::frontend_handler();

		$Frontend->registerLanguageValue("%profile_fields.select_invite_msg.".SK_ProfileFields::get('state_id')->profile_field_id);
		$Frontend->registerLanguageValue("%forms._fields.mileage.tab1");
		$Frontend->registerLanguageValue("%forms._fields.mileage.tab2");
		$Frontend->registerLanguageValue("%forms._fields.mileage.tab3");

		$field_name = $this->getName();
		$lang = SK_Language::section('forms._fields.mileage');
		$value = $this->getValue();

		if (isset($value["country_id"])) {
			$active["tab1"] = "active";
		}elseif (isset($value["mcheck_country"])) {
			$active["tab2"] = "active";
		}elseif (isset($value["radius_third"]) || isset($value["zip_third"])) {
			$active["tab3"] = "active";
		}else {
			$active["tab1"] = "active";
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

		$regions_list = '';
		if (isset($value["mcheck_country"]))
		{
			$result = SK_MySQL::query(SK_MySQL::placeholder("SELECT `Country_str_code` AS `country`, `Region_str_code` AS `region` FROM `" . TBL_LOCATION_COUNTRY . "` WHERE `Country_str_code` IN ('?@')", $value['mcheck_country']));

			$regions = array();
			while ($item = $result->fetch_object()) {
				$regions[$item->region][] = $item->country;
			}

			foreach ($regions as $region => $countries) {
				$regions_list .='<div class="region_value" style="display: none"><span class="region_id">' . $region . '</span>';
					foreach ($countries as $id) {
						$regions_list .='<div class="country">' . $id;
						$regions_list .='</div>';
					}
				$regions_list .='</div>';
			}
		}

		$db_search_unit = SK_Config::section('site')->Section('additional')->Section('profile_list')->search_distance_unit;

		$search_unit = SK_Language::text('i18n.location.' . $db_search_unit);

		$output =
<<<EOT

<div class="field_mileage">

	<ul class="tab_container center">
		<li class="tab prototype_node">
		</li>
	</ul>
	<div class="content_container"></div>

	<div class="tab_1 prototype_node {$active["tab1"]}">
		<div class="tab_content">
			<table class="form">
				<tbody>
					<tr>
						<td width="40%" class="label">
							{$langs["country"]}
						</td>
						<td width="60%" class="value">
							$countries_out
						</td>
					</tr>
					<tr style="display:none">
						<td  class="label">
							{$langs["state"]}
						</td>
						<td class="value">
							<select name="{$field_name}[state_id]">
								$states_value
							</select>
						</td>
					</tr>
					<tr style="display:none">
						<td  class="label mileage_from">
							<div class="radius_first"><input type="text" size="4" name="{$field_name}[radius_first]" value="{$value["radius_first"]}">
							{$lang->text('miles_from', array('unit'=>$search_unit))}</div>
						</td>
						<td class="value">
							<div class="city_name"><input type="text" name="{$field_name}[city_id]" value="{$city_value}" autocomplete=off >&nbsp; {$langs["city"]}</div> &nbsp;

						</td>
					</tr>
					<tr style="display:none">
						<td class="label">
							{$langs["custom_location"]}
						</td>
						<td class="value">
							<input type="text" name="{$field_name}[custom_location]" value="{$value["custom_location"]}">
						</td>
					</tr>
				</tbody>
			</table>


		</div>
	</div>

	<div class="tab_2 prototype_node {$active["tab2"]}">
		<div class="tab_content">
			<div class="tab_values">
				$regions_list
			</div>
			<div class="region_cont">
				<div class="region center"></div>
				<div class="country_list">
					<div class="country prototype_node">
						<input type="checkbox" name="{$field_name}[mcheck_country][]">
						<label></label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="tab_3 prototype_node {$active["tab3"]}">
		<div class="center tab_content">
			<input type="text" size="4" name="{$field_name}[radius_third]" value="{$value["radius_third"]}">&nbsp;{$lang->text('miles_from', array('unit'=>$search_unit))}&nbsp;
			<input type="text" size="9" name="{$field_name}[zip_third]" value="{$value["zip_third"]}">&nbsp;{$lang->text('zip')}
		</div>
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
