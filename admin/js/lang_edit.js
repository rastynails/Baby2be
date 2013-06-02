/**
 * Language tree organize.
 */
function AdminLangEdit( root_sections, languages )
{
	this.languages = languages;
	
	// id-indexed loaded data store
	this.key_nodes = {};
	this.sections = {};
	
	this.appendSections(root_sections, $jq('#admin_lang_tree_container>.branch_bundle'));
	
	
	var handler = this;
	
	// sections tree control
	$jq('#admin_lang_tree_container>.branch_bundle')
		.click(function() {
			handler.selectSection(null);
		});
	
	$jq('#create_lang_section_btn')
		.click(function() {
			handler.createSectionForm();
		})
		.attr('disabled', false);
	
	
	// keys table control
	$jq('#new_lang_key_btn')
		.click(function() {
			handler.createKeyValueForm();
		});
	
	$jq('#edit_lang_key_btn')
		.click(function() {
			handler.createKeyEditForm();
		});
	
	$jq('#delete_lang_key_btn')
		.click(function() {
			handler.deleteKey();
		});
	
	$jq('#edit_lang_key_values_btn')
		.click(function() {
			handler.createValuesEditForm();
		});

}



AdminLangEdit.prototype =
{
	createSectionForm: function()
	{
		$jq('#admin_lang_tree_container').hide();
		
		var section_path = '';
		
		if ( !this.active_section_id ) {
			section_path = '<i style="color: #BFCFDF">root</i>';
		}
		else {
			var sect_id = this.active_section_id;
			while (sect_id != '0') {
				section_path = this.sections[sect_id].section + '.' + section_path;
				sect_id = this.sections[sect_id].parent_section_id;
			}
		}
		
		$jq('#admin_lang_tree_container').after(
			'<table id="new_section_form_tbl" class="edit_section_form_tbl" style="float: left; display: none">'+
				'<thead><tr><td colspan="2">'+section_path+'</td></tr></thead>'+
				'<tbody><tr><td>'+
						'<label for="new_section_input">Section:</label>'+
					'</td><td>'+
						'<input id="new_section_input" type="text" class="input_text" maxlength="60" />'+
					'</td></tr><tr><td>'+
						'<label for="new_section_description_input">Title:</label>'+
					'</td><td>'+
						'<input id="new_section_description_input" type="text" class="input_text" maxlength="255" />'+
					'</td></tr></tbody><tfoot><tr><td colspan="2">'+
					'<div style="float: left">'+
						'<input id="create_lang_section_submit_btn" type="button" class="submit" value="Create" />'+
					'</div><div style="float: right">'+
						'<input id="create_lang_section_cancel_btn" type="button" class="submit" value="Cancel" />'+
					'</div><br clear="all" />'+
				'</td></tr></tfoot>'+
			'</table>'
		);
		
		
		var handler = this;
		
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
				
				if ( !handler.active_section_id ) {
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
							$container = $jq('>ul.branch_bundle', handler.sections[parent_section_id].$node.get(0));
							
							if ( !$container.length ) {
								$container = $jq('<ul class="branch_bundle"></ul>')
									.appendTo( handler.sections[parent_section_id].$node.get(0) );
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
	},
	
	appendSections: function( sections_data, $parent_node )
	{
		var section_id, data, section, ctrl_btn, node;
		for ( section_id in sections_data )
		{
			data = sections_data[section_id];
			
			if ( !this.sections[section_id] ) {
				this.sections[section_id] = {loaded: false, expanded: false, keys_loaded: false};
			}
			
			section = this.sections[section_id];
			
			ctrl_btn = data.has_children
				? ['a', 'class="expand" href="#"']
				: ['span', 'class="childless"'];
			
			section.$node = $jq(
				'<li class="branch_node">'+
					'<'+ctrl_btn.join(' ')+'>&nbsp;</'+ctrl_btn[0]+'>'+
					'<a class="label" href="#" title="'+data.description+'">'+
						data.section+
					'</a>'+
				'</li>');
			
			node = section.$node.get(0);
			
			if ( data.has_children ) {
				section.$expand_btn = $jq('.expand', node)
					.bind('click', this.createNodeClickFunc(section_id));
			}
			
			section.$label = $jq('.label', node)
				.bind('click', this.createLabelClickFunc(section_id));
			
			section.section = data.section;
			section.parent_section_id = data.parent_section_id;
			section.has_children = data.has_children;
			
			$parent_node.append(node);
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
	
	toggleSection: function( section_id, event )
	{
		var section = this.sections[section_id];
		
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
		$jq('#new_lang_key_btn').attr('disabled', 'disabled');
		
		var handler = this,
			$container = $jq(
				'<ul class="branch_bundle"><li style="color:#AFAFAF">Loading...</li></ul>'
			).appendTo(this.sections[section_id].$node.get(0));
		
		$jq.ajax({
			url: URL_ADMIN+'lang_responder.php',
			type: 'POST',
			data: {function_: 'loadSections', parent_section_id: section_id},
			dataType: 'json',
			success: function( data ) {
				$container.empty();
				
				handler.appendSections(data.sections, $container);
				
				var section = handler.sections[section_id];
				section.$expand_btn.attr('className', 'collapse');
				section.loaded = true;
				section.expanded = true;
				
				handler = null;
				$container = null;
			}
		});
	},
	
	collapseSection: function( section_id )
	{
		var section = this.sections[section_id];
		
		$jq('>.branch_bundle', section.$node.get(0)).hide('fast');
		
		section.expanded = false;
		
		section.$expand_btn.attr('className', 'expand');
	},
	
	expandSection: function( section_id )
	{
		var section = this.sections[section_id];
		
		$jq('>.branch_bundle', section.$node.get(0)).show('fast');
		
		section.expanded = true;
		
		section.$expand_btn.attr('className', 'collapse');
	},
	
	
	createLabelClickFunc: function( section_id )
	{
		var handler = this;
		return function( event ) {
			handler.selectSection(section_id, event);
			return false;
		}
	},
	
	
	selectSection: function( section_id, event )
	{
		if ( section_id
			&& section_id == this.active_section_id ) {
			return;
		}
		
		$jq('#admin_lang_tree_container .selected')
			.removeClass('selected');
		
		if ( !section_id ) {
			this.active_section_id = null;
			return;
		}
		
		this.selectKey(null);
		
		var section = this.sections[section_id];
		
		if ( section.has_children && !section.expanded ) {
			this.toggleSection(section_id);
		}
		
		section.$label.addClass('selected');
		
		if ( !section.keys_loaded ) {
			this.loadSectionKeys(section_id);
		}
		else {
			this.displaySectionKeys(section_id);
		}
		
		this.active_section_id = section_id;
	},
	
	
	loadSectionKeys: function( section_id )
	{
		$jq('#admin_lang_key_table > tbody').hide();
		$jq('#admin_lang_key_table_loading').show();
		
		var handler = this;
		
		$jq.ajax({
			url: URL_ADMIN+'lang_responder.php',
			type: 'POST',
			data: {function_: 'loadKeys', section_id: section_id},
			dataType: 'json',
			success: function( data ) {
				handler.sections[section_id].keys_loaded = true;
				handler.displaySectionKeys(section_id, data.keys);
				handler = null;
			}
		});
	},
	
	
	displaySectionKeys: function( section_id, keys )
	{
		this.active_key_id = null;
		
		$jq('#admin_lang_key_table > tbody').hide();
		
		var section = this.sections[section_id];
		
		if ( keys ) {
			section.$keys_tbody =
				this.createSectionKeysTBody(keys);
		}
		else {
			section.$keys_tbody.show();
		}
		
		
		this.displayng_keys_section_id = section_id;
		
		
		$jq('#edit_lang_key_btn').attr('disabled', 'disabled');
		$jq('#delete_lang_key_btn').attr('disabled', 'disabled');
		$jq('#edit_lang_key_values_btn').attr('disabled', 'disabled');
		
		$jq('#new_lang_key_btn').removeAttr('disabled');
	},
	
	
	createSectionKeysTBody: function( keys )
	{
		var $tbody = $jq('<tbody></tbody>').prependTo('#admin_lang_key_table');
		
		var key_id;
		for ( key_id in keys ) {
			$tbody.append(this.createKeyNodeRow(keys[key_id]));
		}
		
		if ( !key_id ) { // no iterations - no keys
			$tbody.append(
				'<tr class="no_keys"><td colspan="2" class="no_keys_in_section">No keys in this section</td></tr>'
			);
		}
		else {
			$jq('>tr', $tbody.get(0)).each(
				function( i ) {
					this.className = (i % 2) ? 'even' : 'odd';
				}
			);
		}
		
		return $tbody;
	},
	
	
	key_display_maxlength: 44,
	
	createKeyNodeRow: function( key_node )
	{
		this.key_nodes[key_node.lang_key_id] = key_node;
		
		/*row_html =
			'<tr id="lang_key_node_row-'+key_node.lang_key_id+'">'+
				'<td class="key_cell">';*/
		row_html =
			'<tr id="lang_key_node_row-'+key_node.lang_key_id+'"><td class="key_container">' +
			'<table width="100%">'+
				'<tr><td class="key_cell">';
		
		if ( key_node.key.length > this.key_display_maxlength ) {
			row_html +=
				'<span title="'+key_node.key+'">'+
					key_node.key.substr(0, this.key_display_maxlength-3)+
				'...</span>';
		}
		else {
			row_html +=
				'<span>'+key_node.key+'</span>';
		}
		
		row_html += '</td></tr>'+
			'<tr><td class="values_cell">'+this.generateValuesTableHtml(key_node.values)+'</td></tr>'+
		'</table></td></tr>';
		
		var $row = $jq(row_html)
			.bind('click', this.createKeySelectFunc(key_node.lang_key_id));
		
		return $row;
	},
	
	
	generateValuesTableHtml: function( values )
	{
		var tbl_html =
			'<table width="100%" class="key_values_tbl">'+
				'<tbody>';
		
		var lang_id;
		for ( lang_id in this.languages )
		{
			tbl_html += '<tr><td width="16">'+this.languages[lang_id].abbrev+'</td>';
			
			if ( values[lang_id] ) {
				tbl_html += '<td><div style="width: 100%; overflow:hidden;">'+values[lang_id]+'</div></td>';
			}
			else {
				tbl_html += '<td class="undefined">undefined</td>';
			}
			
			tbl_html += '</tr>';
		}
		
		tbl_html +=
			'</tbody>'+
		'</table>';
		
		return tbl_html;
	},
	
	
	createKeySelectFunc: function( key_id )
	{
		var handler = this;
		return function( event ) {
			handler.selectKey(key_id, event);
		}
	},
	
	
	selectKey: function( key_id, event )
	{
		if ( key_id && this.active_key_id == key_id ) {
			return;
		}
		
		$jq('#admin_lang_key_table .selected')
			.removeClass('selected');
		
		if ( !key_id ) {
			// disabling key control buttons
			$jq('#edit_lang_key_btn')
			.add('#delete_lang_key_btn')
			.add('#edit_lang_key_values_btn')
				.attr('disabled', 'disabled');
			
			this.active_key_id = null;
			
			return;
		}
		
		this.active_key_id = key_id;
		
		var targ;
		if (event.currentTarget === undefined) { // IE7 case
			targ = jQuery(event.target).parents('.even, .odd').get(0);
		} else {
			targ = event.currentTarget;
		}
		
		$jq(targ).addClass('selected');
		
		this.onKeyChange($jq(targ));
		
		// enabling key control buttons
		$jq('#edit_lang_key_btn')
		.add('#delete_lang_key_btn')
			.removeAttr('disabled');
		
		if ( !this.key_nodes[key_id].values_in_edit ) {
			$jq('#edit_lang_key_values_btn').removeAttr('disabled');
		}
		else {
			$jq('#edit_lang_key_values_btn').attr('disabled', 'disabled');
		}
	},
	
	onKeyChange: function($node) {
		var $controls = $jq('#lang_key_controls');
		$controls.show();
		$node.after($controls);
	},
		
	createKeyValueForm: function( params )
	{
		params = params || {
			section_id: this.displayng_keys_section_id,
			key_id: 0,
			key: '',
			values: {}
		};
		
		var $tbody = this.sections[params.section_id].$keys_tbody;
		
		var $form_row = $jq(
			'<tr class="lang_key_value_form_row '+($tbody.children(':last').hasClass('even')?'odd':'even')+'">'+
				'<td colspan="2" style="padding: 0px"></td>'+
			'</tr>'
		).appendTo($tbody.get(0));
		
		var $form = $jq(
			'<form method="post">'+
				(params.key_id
					? '<input name="lang_key_id" type="hidden" value="'+params.section_id+'" />'
					: '<input name="lang_section_id" type="hidden" value="'+params.section_id+'" />'
				)+
				'<table style="border: none; border-spacing: 0px; width: 100%"><tbody>'+
					'<tr>'+
						'<td width="223"><input name="key" type="text" class="lang_key_input" maxlength="60" /></td>'+
						'<td><table class="key_values_tbl" style="border-spacing: 0px; width: 100%">'+
							'<tbody class="lang_key_values_tbody"></tbody>'+
						'</table></td>'+
					'</tr>'+
				'</tbody></table>'+
			'</form>'
		).appendTo( $jq('>td', $form_row.get(0)).get(0) );
		
		var $values_tbody = $jq('.lang_key_values_tbody', $form.get(0));
		
		var $key_input = $jq('input[@name=key]', $form.get(0));
		
		if ( params.key ) {
			$key_input.val(params.key);
		}
		
		var lang_id, $val_row, $val_cell, $val_input_cont, $val_input,
			$input_switch_cont, $input_switch;
		for ( lang_id in this.languages )
		{
			$val_row = $jq(
				'<tr>'+
					'<td width="16">'+this.languages[lang_id].abbrev+'</td>'+
					'<td class="lang_key_value_cell"></td>'+
				'</tr>'
			).appendTo($values_tbody.get(0));
			
			$val_cell = $jq('.lang_key_value_cell', $val_row.get(0));
			
			$val_input_cont = $jq(
				'<div style="float: left" class="input_con">'+
					'<input name="values['+lang_id+']" class="lang_key_value_input" type="text" />'+
				'</div>'
			).appendTo($val_cell.get(0));
			
			$val_input = $jq('.lang_key_value_input', $val_input_cont.get(0));
			
			if ( params.values[lang_id] ) {
				$val_input.val(params.values[lang_id]);
			}
			
			$input_switch_cont = $jq(
				'<div style="float: right; padding-right: 2px">'+
					'<a class="make_textarea" href="#">&nbsp;</a>'+
				'</div>'
			).appendTo($val_cell.get(0))
			.after('<br clear="all" />');
			
			$input_switch = $jq('.make_textarea', $input_switch_cont.get(0))
				.bind('click', this.createValueInputSwitchFunc($val_input.get(0)));
		}
		
		var $values_tfoot = $jq(
			'<tfoot><tr><td colspan="2">'+
				'<input type="submit" class="save_lang_key_value_btn submit" value="Save" /> '+
				'<input type="button" class="close_key_value_form_btn submit" value="Cancel" />'+
			'</td></tr></tfoot>'
		).appendTo($values_tbody.get(0).parentNode);
		
		
		var handler = this;
		
		$form.submit(function() {
			var form_data = $jq(this).serializeArray(),
				post_data = [],
				lang_key, values_count = 0;
			
			for ( var i = 0, item; item = form_data[i]; i++ )
			{
				if ( item.name == 'key' ) {
					lang_key = item.value;
				}
				else if ( /^values\[\d+\]$/.test(item.name) ) {
					if ( item.value.length ) {
						values_count++;
					}
					else {
						continue;
					}
				}
				
				post_data.push(item);
			}
			
			if ( !lang_key.length ) {
				$jq('input[@name=key]', this).focus();
				return false;
			}
			
			if ( !/^\w+$/.test(lang_key) ) {
				alert('Invalid key syntax');
				$jq('input[@name=key]', this).focus();
				return false;
			}
			
			if ( !values_count ) {
				$jq('.lang_key_value_input:eq(0)', this).focus();
				return false;
			}
			
			$jq.ajax({
				url: URL_ADMIN+'lang_responder.php',
				type: 'POST',
				data: 'function_=processKeyNodeForm&' + $jq.param(post_data),
				dataType: 'json',
				success: function( data )
				{
					$tbody.children('.no_keys').remove();
					
					var $new_row = handler.createKeyNodeRow(data.key_node)
						.addClass($form_row.hasClass('even')?'even':'odd');
					
					$form_row.hide().before($new_row).remove();
				}
			});
			
			return false;
		});
		
		$jq('.close_key_value_form_btn', $values_tfoot.get(0))
			.click(function() {
				$jq(this).parents('.lang_key_value_form_row:eq(0)').remove();
			});
		
		$key_input.focus();
	},
	
	
	createValueInputSwitchFunc: function( input )
	{
		var handler = this;
		return function() {
			handler.switchValueInput(input, this);
			return false;
		}
	},
	
	
	switchValueInput: function( input, switcher )
	{
		if ( $jq(switcher).hasClass('make_textarea') )
		{
			$jq(input).hide().disable();
			
			if ( !input.$textarea_variant )
			{
				input.$textarea_variant =
					$jq('<textarea name="'+input.name+'" class="'+input.className+'"></textarea>')
					.val(input.value)
					.appendTo(input.parentNode)
					.focus();
			}
			else {
				input.$textarea_variant
					.val(input.value)
					.enable()
					.show()
					.focus();
			}
			
			$jq(switcher)
				.removeClass('make_textarea')
				.addClass('make_input_text');
		}
		else { // make input
			var value = input.$textarea_variant.val();
			
			if ( /[\r\n]/m.test(value) )
			{
				if ( !window.confirm(
					'Continuing this operation will remove all line breaks in your text. Are you shure?'
				) ) {
					return false;
				}
				
				value = value.replace(/[\r\n]+/mg, ' ');
			}
			
			input.$textarea_variant.hide().disable();
			
			$jq(input)
				.val(value)
				.enable()
				.show()
				.focus();
			
			$jq(switcher)
				.removeClass('make_input_text')
				.addClass('make_textarea');
		}
	},
	
	
	createKeyEditForm: function()
	{
		var key_node = this.key_nodes[this.active_key_id];
		
		if ( key_node.key_in_edit ) {
			return false;
		}
		
		key_node.key_in_edit = true;
		
		var $key_cell = $jq('#lang_key_node_row-'+this.active_key_id+' .key_cell');
		
		$key_cell.children('span').hide();
		
		var $form = $jq(
			'<form method="post">'+
				'<input name="lang_key_id" type="hidden" value="'+this.active_key_id+'" />'+
				'<input name="key" type="text" class="lang_key_input" maxlength="60" value="'+key_node.key+'" /><br />'+
				'<div class="tfoot_td" style="margin-top: 1px">'+
					'<input type="submit" value="Save" />'+
					'<input name="cancel" type="button" value="Cancel" />'+
				'</div>'+
			'</form>'
		).appendTo($key_cell);
		
		$jq('input[@name=cancel]', $form.get(0))
			.click(function() {
				$form.remove();
				$key_cell.children('span').show();
				key_node.key_in_edit = false;
			});
		
		$form.submit(function()
		{
			var form_data = $jq(this).serializeArray();
			
			for ( var lang_key, i = 0, item; item = form_data[i]; i++ )
			{
				if ( item.name == 'key' ) {
					lang_key = item.value;
				}
			}
			
			if ( !lang_key.length ) {
				$jq('input[@name=key]', this).focus();
				return false;
			}
			
			if ( !/^\w+$/.test(lang_key) ) {
				alert('Invalid key syntax');
				$jq('input[@name=key]', this).focus();
				return false;
			}
			
			$jq.ajax({
				url: URL_ADMIN+'lang_responder.php',
				type: 'POST',
				data: 'function_=processKeyEditForm&' + $jq.param(form_data),
				dataType: 'json',
				success: function( data )
				{
					key_node.key = data.key;
					
					$form.remove();
					
					$key_cell.children('span')
						.empty()
						.text(data.key)
						.show();
					
					key_node.key_in_edit = false;
				}
			});
			
			return false;
		});
		
		$jq('input[@name=key]').focus();
	},
	
	
	
	createValuesEditForm: function()
	{
		var key_node = this.key_nodes[this.active_key_id];
		
		$jq('#edit_lang_key_values_btn').attr('disabled', 'disabled');
		
		key_node.values_in_edit = true;
		
		var handler = this;
		
		$jq.ajax({
			url: URL_ADMIN+'lang_responder.php',
			type: 'POST',
			data: 'function_=loadKeyValuesForEdit&lang_key_id='+key_node.lang_key_id,
			dataType: 'json',
			success: function( data ) {
				handler.drawValuesEditForm(key_node.lang_key_id, data.values);
			}
		});
	},
	
	
	drawValuesEditForm: function( lang_key_id, values )
	{
		var key_node = this.key_nodes[lang_key_id];
		
		var $values_tbl =
			$jq('#lang_key_node_row-'+lang_key_id+' .key_values_tbl')
			.hide();
		
		var $form = $jq(
			'<form method="post">'+
				'<input name="lang_key_id" type="hidden" value="'+lang_key_id+'" />'+
				'<table width="100%" class="key_values_tbl">'+
					'<tbody class="lang_key_values_tbody"></tbody>'+
				'</table>'+
			'</form>'
		).appendTo($values_tbl.parent().get(0));
		
		var $values_tbody = $jq('.lang_key_values_tbody', $form.get(0));
		
		var lang_id, $val_row, $val_cell, $val_input_cont, $val_input,
			$input_switch_cont, $input_switch;
		for ( lang_id in this.languages )
		{
			$val_row = $jq(
				'<tr>'+
					'<td width="16">'+this.languages[lang_id].abbrev+'</td>'+
					'<td class="lang_key_value_cell"></td>'+
				'</tr>'
			).appendTo($values_tbody.get(0));
			
			$val_cell = $jq('.lang_key_value_cell', $val_row.get(0));
			
			$val_input_cont = $jq(
				'<div style="float: left" class="input_con">'+
					'<input name="values['+lang_id+']" class="lang_key_value_input" type="text" />'+
				'</div>'
			).appendTo($val_cell.get(0));
			
			$val_input = $jq('.lang_key_value_input', $val_input_cont.get(0));
			
			$input_switch_cont = $jq(
				'<div style="float: right; padding-right: 2px">'+
					'<a class="make_textarea" href="#">&nbsp;</a>'+
				'</div>'
			).appendTo($val_cell.get(0))
			.after('<br clear="all" />');
			
			$input_switch = $jq('.make_textarea', $input_switch_cont.get(0))
				.bind('click', this.createValueInputSwitchFunc($val_input.get(0)));
			
			if ( values[lang_id] ) {
				if ( !/[\r\n]/m.test(values[lang_id]) ) {
					$val_input.val(values[lang_id]);
				}
				else {
					$input_switch.click();
					$val_input.get(0).$textarea_variant.val(values[lang_id]);
				}
			}
		}
		
		var $values_tfoot = $jq(
			'<tfoot><tr><td colspan="2">'+
				'<input type="submit" value="Save" /> '+
				'<input name="cancel" type="button" value="Cancel" />'+
			'</td></tr></tfoot>'
		).appendTo($values_tbody.parent().get(0));
		
		$jq('input[@name=cancel]', $values_tfoot.get(0))
			.click(function() {
				$form.remove();
				$values_tbl.show();
				key_node.values_in_edit = false;
				$jq('#edit_lang_key_values_btn').removeAttr('disabled');
			});
		
		var handler = this;
		
		$form.submit(function() {
			var form_data = $jq(this).serializeArray(),
				post_data = [],
				values_count = 0;
			
			for ( var i = 0, item; item = form_data[i]; i++ )
			{
				if ( /^values\[\d+\]$/.test(item.name) ) {
					if ( item.value.length ) {
						values_count++;
					}
					else {
						continue;
					}
				}
				
				post_data.push(item);
			}
			
			if ( !values_count ) {
				$jq('.lang_key_value_input:eq(0)', this).focus();
				return false;
			}
			
			$jq.ajax({
				url: URL_ADMIN+'lang_responder.php',
				type: 'POST',
				data: 'function_=updateKeyValues&' + $jq.param(post_data),
				dataType: 'json',
				success: function( data )
				{
					$form.remove();
					$values_tbl.before(
						handler.generateValuesTableHtml(data.values)
					).remove();
					
					key_node.values_in_edit = false;
					$jq('#edit_lang_key_values_btn').removeAttr('disabled');
				}
			});
			
			return false;
		});
		
		$jq('.lang_key_value_input:eq(0)', $form.get(0)).focus();
	},
	
	
	deleteKey: function()
	{
		var key_node = this.key_nodes[this.active_key_id];
		
		if ( !window.confirm('Do you really want to delete key "'+key_node.key+'"?') ) {
			return;
		}
		
		var handler = this
			$keys_tbody = this.sections[this.displayng_keys_section_id].$keys_tbody;
		
		$jq.ajax({
			url: URL_ADMIN+'lang_responder.php',
			type: 'POST',
			data: 'function_=deleteKey&lang_key_id='+key_node.lang_key_id,
			dataType: 'json',
			success: function() {
				if ( handler.active_key_id == key_node.lang_key_id ) {
					handler.selectKey(null);
				}
				$jq('#lang_key_node_row-'+key_node.lang_key_id).remove();
				handler.key_nodes[key_node.lang_key_id] = null;
				
				if ( !$keys_tbody.children().length ) {
					$keys_tbody.append(
						'<tr class="no_keys">'+
							'<td colspan="2" class="no_keys_in_section">No keys in this section</td>'+
						'</tr>'
					);
				}
			}
		});
	}
	
}
