
AdminLangTree = function( root_sections, languages )
{
	this.languages = languages;
	
	this.languages_num = 0;
	for ( var lang_id in languages ) {
		this.languages_num++;
	}
	
	
	this.sections_data = {};
	this.appendSections(root_sections, $jq('#admin_lang_tree_container>.branch_bundle'));
	
	
	var handler = this;
	
	$jq('#admin_lang_tree_container>.branch_bundle')
		.click(function( event ) {
			handler.selectSection(null, event);
		});
	
	$jq('#create_lang_section_btn')
		.click(function( event ) {
			$jq('#admin_lang_tree_container').hide();
			
			var section_path = '';
			
			if ( typeof handler.active_section_id == 'undefined'
				|| !handler.active_section_id ) {
				section_path = '<i style="color: #BFCFDF">root</i>';
			}
			else {
				var sect_id = handler.active_section_id;
				while (sect_id != '0') {
					section_path = handler.sections_data[sect_id].section + '.' + section_path;
					sect_id = handler.sections_data[sect_id].parent_section_id;
				}
			}
			
			$jq('#admin_lang_tree_container').after(
				'<table id="new_section_form_tbl" class="edit_section_form_tbl" style="float: left; display: none">' +
					'<thead><tr><td colspan="2">'+section_path+'</td></tr></thead>' +
					'<tbody><tr><td>' +
							'<label for="new_section_input">Section:</label>' +
						'</td><td>' +
							'<input id="new_section_input" type="text" class="input_text" maxlength="60" />' +
						'</td></tr><tr><td>' +
							'<label for="new_section_description_input">Title:</label>' +
						'</td><td>' +
							'<input id="new_section_description_input" type="text" class="input_text" maxlength="255" />' +
						'</td></tr></tbody><tfoot><tr><td colspan="2">' +
						'<div style="float: left">' +
							'<input id="create_lang_section_submit_btn" type="button" class="submit" value="Create" />' +
						'</div><div style="float: right">' +
							'<input id="create_lang_section_cancel_btn" type="button" class="submit" value="Cancel" />' +
						'</div><br clear="all" />' +
					'</td></tr></tfoot>' +
				'</table>'
			);
			
			$jq('#create_lang_section_submit_btn')
				.click(function() {
					var section = $jq('#new_section_input').val(),
						description = $jq('#new_section_description_input').val(),
						parent_section_id;
					
					if ( !section.length || !/^[a-z0-9_\-]+$/.test(section) ) {
						$jq('#new_section_input').focus();
						return false;
					}
					
					if ( !description.length ) {
						$jq('#new_section_description_input').focus();
						return false;
					}
					
					if ( typeof handler.active_section_id == 'undefined'
						|| !handler.active_section_id ) {
						parent_section_id = '0';
					}
					else {
						parent_section_id = handler.active_section_id;
					}
					
					$jq.ajax({
						url: URL_ADMIN+'lang_responder.php',
						type: 'POST',
						data: { function_: 'createSection',
								parent_section_id: parent_section_id,
								section: section,
								description: description },
						dataType: 'json',
						success: function( data ) {
							var section_id = data.section.lang_section_id,
								$container;
							
							if ( parent_section_id == '0' ) {
								$container = $jq('#admin_lang_tree_container>.branch_bundle');
							}
							else {
								$container = $jq('>ul.branch_bundle', handler.sections_data[parent_section_id].$node.get(0));
								
								if ( !$container.length ) {
									$container = $jq(
										'<ul class="branch_bundle"></ul>'
									).appendTo(handler.sections_data[parent_section_id].$node.get(0));
								}
							}
							
							var _sections = {};
							_sections[section_id] = data.section;
							handler.appendSections(_sections, $container);
							
							$jq('#create_lang_section_cancel_btn').click();
						}
					});
				});
			
			$jq('#create_lang_section_cancel_btn')
				.click(function() {
					$jq('#new_section_form_tbl').remove();
					$jq('#admin_lang_tree_container').show('fast');
				});
			
			$jq('#new_section_form_tbl').show('fast', function() {
				$jq('#new_section_input').focus();
			});
		})
		.attr('disabled', false);
	
	$jq('#new_lang_key_btn')
		.click(function( event ) {
			handler.createKeyValueForm({
				
			});
		});
}



AdminLangTree.prototype =
{
	appendSections: function( sections_data, $parent_node )
	{
		var section_id, $node, $expand_btn, $label;
		for ( section_id in sections_data ) {
			$node = $jq(
				'<li class="branch_node">' +
					'<a class="expand" href="#">&nbsp;</a> ' +
					'<a class="label" href="#" title="'+sections_data[section_id].description+'">'+sections_data[section_id].section+'</a>' +
				'</li>'
			);
			
			$expand_btn = $jq('.expand', $node.get(0)).bind('click', this.createNodeClickFunc(section_id));
			$label = $jq('.label', $node.get(0)).bind('click', this.createLabelClickFunc(section_id));
			
			this.sections_data[section_id] = {
				section: sections_data[section_id].section,
				parent_section_id: sections_data[section_id].parent_section_id,
				$node: $node,
				$expand_btn: $expand_btn,
				$label: $label,
				loaded: false,
				expanded: true
			};
			
			$parent_node.append($node.get(0));
		}
	},
	
	createNodeClickFunc: function( section_id )
	{
		var handler = this;
		return function( event ) {
			handler.toggleSection(section_id, event);
			return false;
		}
	},
	
	createLabelClickFunc: function( section_id )
	{
		var handler = this;
		return function( event ) {
			handler.selectSection(section_id, event);
			return false;
		}
	},
	
	toggleSection: function( section_id, event )
	{
		var section = this.sections_data[section_id];
		
		if ( !section.loaded ) {
			this.loadSection(section_id);
		}
		else if ( section.expanded ) {
			this.collapseSection(section_id);
		}
		else {
			this.expandSection(section_id);
		}
	},
	
	loadSection: function( section_id )
	{
		var handler = this,
			$container = $jq(
				'<ul class="branch_bundle"><li style="color:#AFAFAF">Loading...</li></ul>'
			).appendTo(this.sections_data[section_id].$node.get(0));
		
		$jq.ajax({
			url: URL_ADMIN+'lang_responder.php',
			type: 'POST',
			data: {function_: 'loadSections', parent_section_id: section_id},
			dataType: 'json',
			success: function( data )
			{
				$container.empty();
				with (handler) {
					appendSections(data.sections, $container);
					sections_data[section_id].$expand_btn.attr('className', 'collapse');
					sections_data[section_id].loaded = true;
				}
				$container = null;
				handler = null;
			}
		});
	},
	
	collapseSection: function( section_id )
	{
		$jq('>.branch_bundle', this.sections_data[section_id].$node.get(0)).hide('fast');
		
		with ( this.sections_data[section_id] ) {
			expanded = false;
			$expand_btn.attr('className', 'expand');
		}
	},
	
	expandSection: function( section_id )
	{
		$jq('>.branch_bundle', this.sections_data[section_id].$node.get(0)).show('fast');
		
		with ( this.sections_data[section_id] ) {
			expanded = true;
			$expand_btn.attr('className', 'collapse');
		}
	},
	
	
	selectSection: function( section_id, event )
	{
		$jq('#admin_lang_tree_container .selected').removeClass('selected');
		
		if ( !section_id ) {
			this.active_section_id = null;
			return;
		}
		
		var section = this.sections_data[section_id];
		
		section.$label.addClass('selected');
		
		if ( typeof section.keys == 'undefined' ) {
			this.loadKeys(section_id);
		}
		else {
			this.displayKeys(section_id);
		}
		
		this.active_section_id = section_id;
	},
	
	loadKeys: function( section_id ) {
		$jq('#admin_lang_key_table_container tbody')
			.empty()
			.append('<tr><td colspan="2" style="color:#AFAFAF">Loading...</td></tr>');
		
		var handler = this;
		
		$jq.ajax({
			url: URL_ADMIN+'lang_responder.php',
			type: 'POST',
			data: {function_: 'loadKeys', section_id: section_id},
			dataType: 'json',
			success: function( data )
			{
				with (handler) {
					sections_data[section_id].keys = data.keys;
					displayKeys(section_id);
				}
				handler = null;
			}
		});
	},
	
	key_display_maxlength: 26,
	
	displayKeys: function( section_id )
	{
		var $tbody = $jq('#admin_lang_key_table_container tbody').empty(),
			keys = this.sections_data[section_id].keys,
			key_id, key, html, lang_id, odd = true, row_class,
			value_class_decl;
		
		for ( key_id in keys )
		{
			row_class = (odd = !odd) ? 'odd' : 'even';
			
			key = keys[key_id].key;
			
			html = '<tr class="'+row_class+'">';
			
			if ( key.length > this.key_display_maxlength ) {
				html += '<td><span title="'+key+'">'+( key.substr(0, this.key_display_maxlength-3) )+'...</span></td>';
			}
			else {
				html += '<td><span>'+key+'</span></td>';
			}
			
			html += '<td><table width="100%" class="key_values_tbl"><tbody>';
			
			for ( lang_id in this.languages )
			{
				value_class_decl = (typeof keys[key_id].values[lang_id] == 'undefined') ? ' class="undefined"' : '';
				html += '<tr>' +
							'<td width="16">'+this.languages[lang_id].abbrev+'</td>' +
							'<td'+value_class_decl+'>'+keys[key_id].values[lang_id]+'</td>' +
						'</tr>';
			}
			
			html += '</tbody></table></td></tr>';
			
			$jq(html).appendTo($tbody.get(0))
				.bind('click', this.createKeySelectFunc(key_id));
		}
		
		this.active_key_id = null;
		
		if ( !$jq('#edit_lang_key_btn').attr('disabled') ) {
			$jq('#edit_lang_key_btn').attr('disabled', true);
		}
		
		if ( !$jq('#delete_lang_key_btn').attr('disabled') ) {
			$jq('#delete_lang_key_btn').attr('disabled', true);
		}
		
		if ( !$jq('#edit_lang_key_values_btn').attr('disabled') ) {
			$jq('#edit_lang_key_values_btn').attr('disabled', true);
		}
		
		if ( $jq('#new_lang_key_btn').attr('disabled') ) {
			$jq('#new_lang_key_btn').attr('disabled', false);
		}
		
		this.displayng_keys_section_id = section_id;
	},
	
	createKeySelectFunc: function( key_id )
	{
		var handler = this;
		return function( event ) {
			handler.selectKey(key_id, event);
			return false;
		}
	},
	
	selectKey: function( key_id, event )
	{
		$jq('#admin_lang_key_table_container .selected').removeClass('selected');
		$jq(event.currentTarget).addClass('selected');
		
		if ( $jq('#edit_lang_key_btn').attr('disabled') ) {
			$jq('#edit_lang_key_btn').attr('disabled', false);
		}
		
		if ( $jq('#delete_lang_key_btn').attr('disabled') ) {
			$jq('#delete_lang_key_btn').attr('disabled', false);
		}
		
		if ( $jq('#edit_lang_key_values_btn').attr('disabled') ) {
			$jq('#edit_lang_key_values_btn').attr('disabled', false);
		}
				
		this.active_key_id = key_id;
		
	},
	
	
	createKeyValueForm: function( params )
	{
		var $tbody = $jq('#admin_lang_key_table_container tbody'),
			row_class = ($tbody.children(':last-child').hasClass('even')) ? 'odd' : 'even';
		
		var $form_row = $jq(
			'<tr class="lang_key_value_form_row '+row_class+'">' +
				'<td><input name="key" type="text" class="lang_key_input" maxlength="60" /></td>' +
				'<td><table class="key_values_tbl" width="100%"><tbody class="lang_key_values_tbody"></tbody></table></td>' +
			'</tr>').appendTo($tbody.get(0));
		
		var $values_tbody = $jq('.lang_key_values_tbody', $form_row.get(0));
		
		var $val_row, $val_cell, $val_input_cont, $val_input, $input_switch_cont, $input_switch;
		for ( lang_id in this.languages )
		{
			$val_row = $jq(
				'<tr><td width="16">'+this.languages[lang_id].abbrev+'</td><td class="lang_key_value_cell"></td></tr>'
				).appendTo($values_tbody.get(0));
			
			$val_cell = $jq('.lang_key_value_cell', $val_row.get(0));
			
			$val_input_cont = $jq(
				'<div style="float: left">' +
					'<input name="value_for_'+lang_id+'" type="text" class="lang_key_value_input" />' +
				'</div>').appendTo($val_cell.get(0));
			
			$val_input = $jq('.lang_key_value_input', $val_input_cont.get(0));
			
			$input_switch_cont = $jq(
				'<div style="float: right; padding-right: 2px">' +
					'<a class="make_textarea" href="#">&nbsp;</a>' +
				'</div>').appendTo($val_cell.get(0)).after('<br clear="all" />');
			
			$input_switch = $jq('.make_textarea', $input_switch_cont.get(0))
				.bind('click', this.createValueInputSwitchFunc($val_input.get(0)));
		}
		
		var $values_tfoot = $jq(
			'<tfoot><tr><td colspan="2">' +
				'<input type="button" class="save_lang_key_value_btn submit" value="Save" /> ' +
				'<input type="button" class="close_key_value_form_btn submit" value="Cancel" />' +
			'</td></tr></tfoot>'
		).appendTo($values_tbody.get(0).parentNode);
		
		
		$jq('.save_lang_key_value_btn', $values_tfoot.get(0))
			.click(function() {
				
			});
		
		$jq('.close_key_value_form_btn', $values_tfoot.get(0))
			.click(function() {
				$jq(this).parents('.lang_key_value_form_row:eq(0)').remove();
			});
		
	},
	
	
	createValueInputSwitchFunc: function( input )
	{
		var handler = this;
		return function( event ) {
			handler.switchValueInput(input, event);
			return false;
		}
	},
	
	
	switchValueInput: function( input, event )
	{
		if ( typeof input.$textarea_variant == 'undefined' )
		{
			$jq(input).hide();
			
			input.$textarea_variant = $jq(
				'<textarea name="'+input.name+'" class="'+input.className+'"></textarea>'
				).appendTo(input.parentNode).val(input.value).focus();
			
			$jq(event.currentTarget)
				.removeClass('make_textarea')
				.addClass('make_input_text');
		}
		
		else if ( $jq(event.currentTarget).hasClass('make_input_text') )
		{
			var nl_pos = input.$textarea_variant.val().search(/\n/m);
			
			if ( nl_pos !== -1 )
			{
				if ( !window.confirm(
					'Text will be truncated up to one line, continue?'
				) ) {
					return false;
				}
				
				input.$textarea_variant.val(
					input.$textarea_variant.val().substr(0, nl_pos)
				);
			}
			
			input.$textarea_variant.hide();
			
			input.value = input.$textarea_variant.val();
			$jq(input).show();
			
			$jq(event.currentTarget)
				.removeClass('make_input_text')
				.addClass('make_textarea');
		}
		else {
			$jq(input).hide();
			
			input.$textarea_variant.val(input.value);
			input.$textarea_variant.show();
			
			$jq(event.currentTarget)
				.removeClass('make_textarea')
				.addClass('make_input_text');
		}
	}
	
	
	
	
}
