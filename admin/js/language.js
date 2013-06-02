
var SK_AdminLangEdit = {
	requestValuesEdition: function(btn_node, lang_section, lang_key, options)
	{
		jQuery(btn_node).disable();

		options = options || {};

		var handler = this;
		var request_data = {
			function_: 'loadKeyValuesForEdit',
			lang_section: lang_section,
			lang_key: lang_key,
			lang_id: options.lang_id || null
		}
		if (!this.languages) {
			request_data.get_languages = true;
		}

		jQuery.ajax({
			url: URL_ADMIN+'lang_responder.php',
			type: 'POST',
			data: request_data,
			dataType: 'json',
			success: function(data)
			{


				if (data.languages) {
					handler.languages = data.languages;
					handler.default_lang_id = data.default_lang_id;
				}
				jQuery(btn_node).enable();

				handler.drawValuesEditForm(btn_node, data, data.demoMode);
				handler = null;
			}
		});
	},

	drawValuesEditForm: function(btn_node, data, demoMode)
	{
                demoMode = demoMode || false;

		var $form = $jq(
			'<form method="post">'+
				'<input name="lang_key_id" type="hidden" value="'+data.lang_key_id+'" />'+
				'<table width="100%" class="lang_values_edit_form_tbl">'+
					'<tbody class="lang_key_values_tbody"></tbody>'+
				'</table>'+
			'</form>'
		);

		var $btn_node = jQuery(btn_node).hide().before($form);

		var $values_tbody = $jq('.lang_key_values_tbody', $form.get(0));

		var lang_id, $val_row, $val_cell, $val_input_cont, $val_input,
			$input_switch_cont, $input_switch;
		for (lang_id in this.languages)
		{
			$val_row = $jq(
				'<tr>'+
					'<td width="16">'+this.languages[lang_id].abbrev+'</td>'+
					'<td class="lang_key_value_cell"></td>'+
				'</tr>'
			).appendTo($values_tbody.get(0));

			$val_cell = $jq('.lang_key_value_cell', $val_row.get(0));

			$val_input_cont = $jq(
				'<div style="float: left">'+
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

			if (data.values[lang_id]) {
				if (!/[\r\n]/m.test(data.values[lang_id])) {
					$val_input.val(data.values[lang_id]);
				}
				else {
					$input_switch.click();
					$val_input.get(0).$textarea_variant.val(data.values[lang_id]);
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
				$btn_node.show();
			});

		var handler = this;

		$form.submit(function() {

                        if ( demoMode )
                        {
                            alert('No changes made. Demo mode.');
                            return false;
                        }

			var form_data = $jq(this).serializeArray(),
				post_data = [],
				values_count = 0;
				regex = /^values\[(\d+)\]$/;
			for (var i = 0, item; item = form_data[i]; i++)
			{
				if (regex.test(item.name)) {
					if (item.value.length) {
						values_count++;
					}
					else if ( regex.exec(item.name)[1] == data.default_lang_id ) {
						return false;
					}
					else {
						continue;
					}
				}

				post_data.push(item);
			}

			if (!values_count) {
				$jq('.lang_key_value_input:eq(0)', this).focus();
				return false;
			}

			$jq.ajax({
				url: URL_ADMIN+'lang_responder.php',
				type: 'POST',
				data: 'function_=updateKeyValues&' + $jq.param(post_data),
				dataType: 'json',
				success: function(data) {
					$form.remove();
					$btn_node.html(data.values[handler.default_lang_id]).show();
				}
			});

			return false;
		});

		$jq('.lang_key_value_input:eq(0)', $form.get(0)).focus();
	},


	createValueInputSwitchFunc: function(input)
	{
		var handler = this;
		return function() {
			handler.switchValueInput(input, this);
			return false;
		}
	},


	switchValueInput: function(input, switcher)
	{
		if ($jq(switcher).hasClass('make_textarea'))
		{
			$jq(input).hide().disable();

			if (!input.$textarea_variant)
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

			if (/[\r\n]/m.test(value))
			{
				if (!window.confirm(
					'Continuing this operation will remove all line breaks in your text. Are you shure?'
				)) {
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
	}

}



function select_tab(obj, input_name, lang_id)
{
	$jq(obj).parent().find('.active_lang_tab').attr('className','lang_tab');
	$jq(obj).attr('className','active_lang_tab');
	$jq.each($jq(obj).parent().parent().find('.lang_val_input'),function(i,object){
		if($jq(object).attr('name') == input_name+'['+lang_id+']')
			$jq(object).css('display','inline');
		else
			$jq(object).css('display','none');

		if($jq.trim($jq(object).attr('id')))
		{
			$jq(object).focus(function(){
				$jq(this).parent().find('.lang_val_input').css('display','none');
				$jq(obj).parent().parent().find('.active_lang_tab').attr('className','lang_tab');
				$jq('#'+input_name+'_id').attr('className','active_lang_tab');
				$jq(object).css('display','inline');
			});
		}

	});
}

function lang_check_values(obj, msg, showAlert)
{
    showAlert = showAlert === false ? false : true;

    var r = false;
    var lang, name, tab;

    $jq(obj).find('input, textarea').each(function(){
        if ( !this.value ) {
            r = this;
            return false;
        }
    });

    if ( r )
    {
        if ( showAlert )
        {
            lang = $jq(r).attr('data-lang');
            name = $jq(r).attr('data-name');
            tab = $jq('.lang_tab_' + name + '_' + lang).get(0);
            select_tab(tab, name, lang);

            $jq(r).focus();
            alert(msg);
        }

        return false;
    }

    return true;
}



function manageLanguageImportType( import_type )
{
	switch ( import_type )
	{
		case 'update':
			$( 'new_lang_label' ).disabled = true;
			$( 'new_lang_abr' ).disabled = true;
			$( 'updated_lang_id' ).disabled = false;
			break;
		case 'insert':
			$( 'new_lang_label' ).disabled = false;
			$( 'new_lang_abr' ).disabled = false;
			$( 'updated_lang_id' ).disabled = true;
			break;
	}
}

